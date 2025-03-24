FROM php:8.2-fpm

LABEL maintainer="Pivot Marketplace <contact@pivot.fr>"

ENV DEBIAN_FRONTEND=noninteractive

# Installation des dépendances en une seule couche
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    git \
    nginx \
    supervisor \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    gnupg \
    ca-certificates \
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_18.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        intl \
        opcache \
    && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration de Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Configuration de Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copie du script de configuration des images
COPY docker/setup-images.sh /usr/local/bin/setup-images.sh
RUN chmod +x /usr/local/bin/setup-images.sh

# Préparation des dossiers
WORKDIR /var/www
RUN mkdir -p /var/www/public/imagesAccueil \
    && chown -R www-data:www-data /var/www

# Copie des fichiers composer.json et package.json pour mise en cache
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Installation des dépendances Composer et NPM avec cache
RUN composer install --no-scripts --no-autoloader --no-dev --quiet \
    && npm ci --quiet --cache /tmp/npm-cache

# Copie du reste du code source
COPY . .

# Fix pour storage/logs symlink
RUN rm -rf storage/logs && mkdir -p /var/log/laravel \
    && ln -sf /var/log/laravel storage/logs \
    && mkdir -p storage/app/public \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www

# Création du lien symbolique storage et setup des images
RUN php artisan storage:link || true \
    && /usr/local/bin/setup-images.sh

# Construction des assets et finalisation
USER www-data
RUN composer dump-autoload --optimize --no-dev --quiet \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && npm run build --quiet

# Retour à l'utilisateur root
USER root

# Exposition du port et définition de l'entrypoint
EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 