FROM php:8.2-fpm AS base

# Arguments définissant la configuration
ARG BUILDKIT_INLINE_CACHE=1
ARG APP_ENV=production
ARG NODE_VERSION=20

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
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        mysqli \
        intl

# Installation de Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get update && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/* \
    && npm install -g npm@latest

# Installation de Composer
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

# Préparation de la structure de répertoires
RUN mkdir -p /var/www/storage/app/public \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/shm/laravel-logs \
    /var/www/bootstrap/cache \
    /var/www/public/images \
    /var/www/public/build/assets \
    /var/www/docker

# Créer explicitement storage/logs comme un répertoire (sera remplacé par un symlink plus tard)
RUN mkdir -p /var/www/storage/logs

WORKDIR /var/www

# Copie des fichiers de configuration
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /var/www/docker/entrypoint.sh
RUN chmod +x /var/www/docker/entrypoint.sh

# Ajouter les fichiers de diagnostic
RUN echo '<?php phpinfo();' > /var/www/public/info.php \
    && echo '<?php echo json_encode(["status" => "ok", "timestamp" => time()]);' > /var/www/public/health-check.php

# Copier uniquement les fichiers de dépendances pour optimiser le cache
COPY composer.json composer.lock ./

# Installer les dépendances PHP avec output réduit et optimisation du cache
RUN COMPOSER_ALLOW_SUPERUSER=1 \
    composer install \
        --no-autoloader \
        --no-scripts \
        --no-dev \
        --no-interaction \
        --no-ansi \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader

# Copier le code source
COPY --chown=www-data:www-data . .

# Supprimer le répertoire storage/logs existant pour le remplacer par un symlink
RUN rm -rf /var/www/storage/logs && \
    ln -sf /var/shm/laravel-logs /var/www/storage/logs

# Copier les paramètres d'environnement spécifiques
COPY docker/env.production /var/www/.env

# Générer l'autoloader optimisé et mettre en cache les configurations
RUN COMPOSER_ALLOW_SUPERUSER=1 \
    composer dump-autoload --no-dev --optimize \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php -l public/index.php \
    && php -l bootstrap/app.php

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
    /var/www/.git \
    /var/www/.github \
    /var/www/docker/env.* \
    && find /var/www -name ".git*" -type f -delete \
    && composer clear-cache

# Exposition du port
EXPOSE 4004

# Point d'entrée
ENTRYPOINT ["/var/www/docker/entrypoint.sh"]

# Commande de secours en cas d'échec d'ENTRYPOINT
CMD ["/bin/bash", "-c", "chmod +x /var/www/docker/entrypoint.sh && /var/www/docker/entrypoint.sh"] 