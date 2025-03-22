FROM php:8.2-fpm AS base

# Arguments pour le cache-busting
ARG BUILDKIT_INLINE_CACHE=1

# Installation des dépendances système en une seule couche
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    iputils-ping \
    net-tools \
    dnsutils \
    telnet \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip mysqli intl \
    && mkdir -p /var/www/storage/app/public \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/shm/laravel-logs \
    /var/www/bootstrap/cache \
    /var/www/public/images \
    /var/www/public/build/assets \
    /var/www/docker

# Installation de Node.js
FROM base AS node
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# Installation de Composer et préparation de l'environnement PHP
FROM node AS composer
COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

# Configuration de PHP et PHP-FPM
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 600" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "log_errors = On" > /usr/local/etc/php/conf.d/error-log.ini \
    && echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/error-log.ini \
    && echo "catch_workers_output = yes" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "php_admin_value[error_log] = /dev/stderr" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "listen = 127.0.0.1:9000" > /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www

# Copie des fichiers de configuration
FROM composer AS config
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /var/www/docker/entrypoint.sh
RUN chmod +x /var/www/docker/entrypoint.sh

# Ajout des diagnostics et des scripts de santé
RUN echo '<?php phpinfo();' > /var/www/public/info.php \
    && echo '<?php echo json_encode(["status" => "ok", "env" => $_SERVER, "timestamp" => time()]);' > /var/www/public/health-check.php \
    && ln -sf /dev/shm/laravel-logs /var/www/storage/logs

# Etape de build de l'application
FROM config AS builder
COPY composer.json composer.lock ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-autoloader --no-scripts --no-dev

# Copie de l'application
COPY . .
COPY docker/env.production /var/www/.env

# Installation des dépendances et optimisation
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-dev \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php -l public/index.php \
    && php -l bootstrap/app.php

# Étape finale
FROM builder AS final

# Configuration des permissions
RUN chmod -R 777 /var/www/storage \
    && chmod -R 777 /var/www/bootstrap/cache \
    && chmod -R 777 /dev/shm/laravel-logs \
    && chown -R www-data:www-data /var/www \
    && chown -R www-data:www-data /dev/shm/laravel-logs \
    && touch /dev/shm/laravel-logs/laravel.log \
    && chmod 666 /dev/shm/laravel-logs/laravel.log \
    && chmod +x /var/www/docker/entrypoint.sh

# Nettoyage pour réduire la taille de l'image
RUN rm -rf /var/www/node_modules \
    && find /var/www -name ".git*" -type f -delete

# Exposition du port
EXPOSE 4004

# Point d'entrée
ENTRYPOINT ["/var/www/docker/entrypoint.sh"]

# Commande de secours en cas d'échec d'ENTRYPOINT
CMD ["/bin/bash", "-c", "chmod +x /var/www/docker/entrypoint.sh && /var/www/docker/entrypoint.sh"] 