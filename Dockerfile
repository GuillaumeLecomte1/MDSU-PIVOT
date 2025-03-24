FROM php:8.2-fpm as php

# Set non-interactive mode for apt
ENV DEBIAN_FRONTEND=noninteractive

# Install dependencies and PHP extensions efficiently
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx supervisor curl zip unzip git \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev libicu-dev libonig-dev \
    nodejs npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    # Install PHP extensions efficiently (combined in one layer)
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql mbstring gd zip intl opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set up working directory
WORKDIR /var/www

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Create Laravel directory structure
RUN mkdir -p \
    /var/www/storage/app/public \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/bootstrap/cache \
    /var/log/laravel \
    /var/www/public/build \
    # Create logs directory as a symlink to external storage
    && ln -sf /var/log/laravel /var/www/storage/logs

# Copy application code with correct permissions
COPY --chown=www-data:www-data . /var/www

# Set proper permissions
RUN chown -R www-data:www-data /var/www /var/log/laravel \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Switch to www-data user for dependency installation
USER www-data

# Install dependencies and build assets
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm ci --no-audit --no-fund \
    && npm run build \
    && rm -rf node_modules

# Switch back to root for running services
USER root

# Set up PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini

# Clear cache to reduce image size
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose port
EXPOSE 4004

# Set entrypoint and command
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 