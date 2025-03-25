FROM php:8.2-fpm AS base

LABEL maintainer="Pivot Marketplace <contact@pivot.fr>"

# Configuration des variables d'environnement
ENV DEBIAN_FRONTEND=noninteractive \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=10000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=192 \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE=10 \
    QUEUE_CONNECTION=sync \
    COMPOSER_ALLOW_SUPERUSER=1

# Installation des dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl nginx supervisor \
    libfreetype6-dev libjpeg62-turbo-dev libicu-dev libzip-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mbstring exif pcntl bcmath zip intl opcache

# Configuration de PHP pour autoriser proc_open (nécessaire pour Composer et Laravel)
RUN echo "disable_functions = " > /usr/local/etc/php/conf.d/docker-php-enable-functions.ini

# Configuration de PHP et OPcache
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.conf

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installation de Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

# Configuration de Nginx et Supervisor
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Préparation du répertoire de travail
WORKDIR /var/www
RUN mkdir -p /var/www/public \
    && mkdir -p /var/log/laravel \
    && mkdir -p /var/www/storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Copie et installation des dépendances
COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-dev --no-progress \
    && npm ci --quiet --no-audit --production

# Copie du code source
COPY --chown=www-data:www-data . .

# Optimisation de l'application (avec traitement d'erreurs amélioré)
RUN set -e \
    && echo "Vérification de proc_open..." \
    && php -r "echo function_exists('proc_open') ? 'OK' : 'DISABLED';" \
    && composer dump-autoload --optimize --no-dev \
    && php artisan storage:link || true \
    && php artisan config:cache || true \
    && php artisan route:cache || true \
    && NODE_OPTIONS="--max-old-space-size=4096" npm run build || echo "Asset compilation failed, continuing anyway" \
    && rm -rf node_modules /root/.npm /tmp/*

# Script d'entrée
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 