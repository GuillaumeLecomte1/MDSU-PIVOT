FROM php:8.2-fpm

# Install system dependencies (minimisé)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js 20.x (version minimale)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@latest && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions with mysqli
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip mysqli

# Get Composer
COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Create necessary directories
RUN mkdir -p /var/www/storage/app/public \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache \
    && mkdir -p /var/www/public/images \
    && mkdir -p /var/www/public/build/assets/js \
    && mkdir -p /var/www/public/build/assets/css \
    && mkdir -p /var/www/public/build/.vite

# Configure PHP for better performance and increased upload limits
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Configurer Nginx
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Configurer Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copier les fichiers de configuration
COPY docker/fix-vite-issues.php /var/www/docker/fix-vite-issues.php
COPY docker/fix-https-urls.php /var/www/docker/fix-https-urls.php
COPY docker/fix-env.sh /var/www/docker/fix-env.sh
COPY docker/fix-pusher.php /var/www/docker/fix-pusher.php
COPY docker/fix-mixed-content.php /var/www/docker/fix-mixed-content.php
COPY docker/optimize-laravel.sh /var/www/docker/optimize-laravel.sh
COPY docker/entrypoint.sh /var/www/docker/entrypoint.sh
COPY permissions.sh /var/www/docker/permissions.sh

# Donner les permissions d'exécution aux scripts
RUN chmod +x /var/www/docker/fix-vite-issues.php /var/www/docker/fix-https-urls.php /var/www/docker/fix-env.sh /var/www/docker/fix-pusher.php /var/www/docker/fix-mixed-content.php /var/www/docker/optimize-laravel.sh /var/www/docker/permissions.sh /var/www/docker/entrypoint.sh

# Copier le code source
COPY . /var/www/

# Installer les dépendances PHP (sans developpement)
RUN cd /var/www && \
    COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-dev

# Préparer les assets statiques (SANS UTILISER VITE EN PRODUCTION)
RUN cd /var/www && \
    echo "Utilisation du mode de compatibilité pour les assets..." && \
    mkdir -p /var/www/public/build/assets/js && \
    mkdir -p /var/www/public/build/assets/css && \
    mkdir -p /var/www/public/build/.vite && \
    cp /var/www/resources/js/app.minimal.jsx /var/www/public/build/assets/js/app.js && \
    echo "/* Styles CSS de base */" > /var/www/public/build/assets/css/app.css && \
    echo '{"resources/js/app.jsx":{"file":"assets/js/app.js","isEntry":true,"src":"resources/js/app.jsx"},"resources/js/app.minimal.jsx":{"file":"assets/js/app.js","isEntry":true,"src":"resources/js/app.minimal.jsx"},"resources/css/app.css":{"file":"assets/css/app.css","isEntry":true,"src":"resources/css/app.css"}}' > /var/www/public/build/manifest.json && \
    echo '{"resources/js/app.jsx":{"file":"assets/js/app.js","isEntry":true,"src":"resources/js/app.jsx"},"resources/js/app.minimal.jsx":{"file":"assets/js/app.js","isEntry":true,"src":"resources/js/app.minimal.jsx"},"resources/css/app.css":{"file":"assets/css/app.css","isEntry":true,"src":"resources/css/app.css"}}' > /var/www/public/build/.vite/manifest.json && \
    php /var/www/docker/fix-https-urls.php && \
    php /var/www/docker/fix-mixed-content.php

# Définir les permissions correctement
RUN chmod -R 777 /var/www/storage && \
    chmod -R 777 /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www && \
    touch /var/www/storage/logs/laravel.log && \
    chmod 666 /var/www/storage/logs/laravel.log && \
    chmod -R 755 /var/www/public/build

# Exposer le port
EXPOSE 4004

# Définir l'entrée
ENTRYPOINT ["/var/www/docker/entrypoint.sh"] 