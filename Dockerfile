FROM php:8.2-fpm-bullseye

# Use Debian Bullseye slim which already has most extensions
LABEL maintainer="guillaume.lecomte049@outlook.fr"

# Suppress warnings and non-critical messages during apt install
ENV DEBIAN_FRONTEND=noninteractive

# Install dependencies in a single RUN command to minimize layers and reduce log output
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    wget \
    zip \
    unzip \
    nginx \
    supervisor \
    # Node.js and npm via nodesource repo
    gnupg \
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_18.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* 

# Install pre-built PHP extensions using the official helper script
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    opcache

# Install additional PHP extensions using PECL
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    zip \
    gd \
    intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer using the official installer script
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set up working directory
WORKDIR /var/www

# Copy configuration files first (these rarely change)
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf 
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Create necessary directories and handle logs properly
RUN mkdir -p \
    /var/www/storage/app/public \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/bootstrap/cache \
    /var/www/public/build \
    /var/log/laravel \
    # Important: Remove the destination before creating symlink
    && rm -rf /var/www/storage/logs \
    && ln -sf /var/log/laravel /var/www/storage/logs

# Copy composer.json and package.json files first for better layer caching
COPY composer.json composer.lock ./
COPY package*.json ./
COPY vite.config.js* ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --quiet

# Copy the application code EXCEPT storage/logs (use .dockerignore for this)
# Ensure we don't copy storage/logs which is a symlink
COPY --chown=www-data:www-data . .

# Fix any potential issues with storage/logs
RUN rm -rf storage/logs && ln -sf /var/log/laravel storage/logs

# Make storage directory accessible and create public storage link
RUN php artisan storage:link || true

# Create directory for images if it doesn't exist
RUN mkdir -p public/imagesAccueil

# Set proper permissions
RUN chown -R www-data:www-data . \
    && chmod -R 755 storage bootstrap/cache \
    && chmod -R 755 /var/log/laravel

# Switch to www-data for dependency and asset operations
USER www-data

# Fix for Vite asset loading - modify path in Welcome.jsx
RUN if [ -f resources/js/Pages/Welcome.jsx ]; then \
    sed -i 's|\/storage\/app\/public\/imagesAccueil|\/imagesAccueil|g' resources/js/Pages/Welcome.jsx; \
fi

# Finish Composer installation and build assets with error handling
RUN composer dump-autoload -o --quiet \
    && npm ci --quiet \
    && npm run build || echo "Asset build failed but continuing" \
    && rm -rf node_modules

# Switch back to root
USER root

# PHP production settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini

# Laravel optimization (only do this after all code is copied)
RUN su -s /bin/sh -c "php artisan config:cache" www-data \
    && su -s /bin/sh -c "php artisan route:cache" www-data \
    && su -s /bin/sh -c "php artisan view:cache" www-data

# Expose port
EXPOSE 4004

# Start services
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 