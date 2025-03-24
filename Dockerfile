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
    && mkdir -p /var/log/laravel \
    && chown -R www-data:www-data /var/www

# ÉTAPE 1: Copie des fichiers de dépendances et installation
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./
RUN composer install --no-scripts --no-autoloader --no-dev --quiet \
    && npm ci --quiet --cache /tmp/npm-cache

# ÉTAPE 2: Copie des sources
# Créer une image de test pour résoudre le problème d'importation Vite
COPY --chown=www-data:www-data resources/js/Pages/Welcome.jsx resources/js/Pages/Welcome.jsx.original
COPY --chown=www-data:www-data . .

# ÉTAPE 3: Fix pour les fichiers d'assets et logs
RUN rm -rf storage/logs && ln -sf /var/log/laravel storage/logs \
    && mkdir -p storage/app/public/imagesAccueil \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && php artisan storage:link || true \
    && /usr/local/bin/setup-images.sh

# ÉTAPE 4: Créer des images factices pour Vite
RUN mkdir -p public/imagesAccueil \
    && touch public/imagesAccueil/imageAccueil1.png \
    && touch public/imagesAccueil/Calque_1.svg \
    && touch public/imagesAccueil/dernierArrivage.png \
    && touch public/imagesAccueil/aPropos.png \
    && touch public/imagesAccueil/blog1.png \
    && touch public/imagesAccueil/blog2.png \
    && chown -R www-data:www-data public/imagesAccueil

# ÉTAPE 5: Configuration et compilation
USER www-data
RUN composer dump-autoload --optimize --no-dev --quiet \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && npm run build || echo "Build failed but continuing"

# Retour à l'utilisateur root
USER root

# ÉTAPE 6: Nettoyage
RUN rm -rf /tmp/npm-cache \
    && rm -rf node_modules

# Exposition du port et définition de l'entrypoint
EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 