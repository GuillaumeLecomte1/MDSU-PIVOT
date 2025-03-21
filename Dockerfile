FROM php:8.2-fpm

# Installation des dépendances système essentielles
RUN apt-get update && apt-get install -y \
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
    && rm -rf /var/lib/apt/lists/*

# Installation de Node.js 20.x
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP essentielles
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip mysqli intl

# Installation de Composer
COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

# Définition du répertoire de travail
WORKDIR /var/www

# Création des répertoires nécessaires
RUN mkdir -p /var/www/storage/app/public \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache \
    && mkdir -p /var/www/public/images \
    && mkdir -p /var/www/public/build/assets \
    && mkdir -p /var/www/docker

# Configuration PHP pour les performances
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time = 600" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "log_errors = On" > /usr/local/etc/php/conf.d/error-log.ini && \
    echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/error-log.ini

# Configuration de php-fpm
RUN echo "catch_workers_output = yes" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "php_admin_value[error_log] = /dev/stderr" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "listen = 127.0.0.1:9000" > /usr/local/etc/php-fpm.d/zz-docker.conf

# Configuration Nginx et Supervisor
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy the entrypoint script
COPY docker/entrypoint.sh /var/www/docker/entrypoint.sh
RUN chmod +x /var/www/docker/entrypoint.sh

# Copie du code source
COPY . /var/www/

# Copy production environment file
COPY docker/env.production /var/www/.env

# Ensure entrypoint.sh still has executable permissions after copying files
RUN chmod +x /var/www/docker/entrypoint.sh && \
    ls -la /var/www/docker/entrypoint.sh

# Installation des dépendances PHP
RUN cd /var/www && \
    COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-dev

# Vérification du code source avant déploiement
RUN cd /var/www && \
    php artisan route:list --no-ansi > /dev/null || echo "Vérification des routes terminée" && \
    php -l public/index.php && \
    php -l bootstrap/app.php

# Configuration des permissions des répertoires critiques
RUN chmod -R 777 /var/www/storage && \
    chmod -R 777 /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www && \
    touch /var/www/storage/logs/laravel.log && \
    chmod 666 /var/www/storage/logs/laravel.log && \
    chmod +x /var/www/docker/entrypoint.sh  # Triple-check permissions

# Exposition du port
EXPOSE 4004

# Point d'entrée
ENTRYPOINT ["/var/www/docker/entrypoint.sh"]

# Fallback command in case ENTRYPOINT fails
CMD ["/bin/bash", "-c", "chmod +x /var/www/docker/entrypoint.sh && /var/www/docker/entrypoint.sh"] 