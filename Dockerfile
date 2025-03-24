FROM php:8.2-fpm

# Add maintainer information
LABEL maintainer="Your Name <guillaume.lecomte049@outlook.fr>"

# Set arguments for build
ARG USER=www-data
ARG GROUP=www-data
ARG DEBIAN_FRONTEND=noninteractive

# Set working directory
WORKDIR /var/www

# Install dependencies and utilities in a single layer to reduce image size
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    nginx supervisor \
    nodejs npm \
    && rm -rf /var/lib/apt/lists/* 

# Install PHP extensions using the docker-php-ext-install script more reliably
# Use separate RUN commands for problematic extensions to avoid build failures
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath

# Install GD extension separately
RUN docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd

# Install ZIP extension separately
RUN docker-php-ext-install zip

# Install intl extension separately
RUN docker-php-ext-configure intl && \
    docker-php-ext-install intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create required directories
RUN mkdir -p /var/www/storage/app/public \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/bootstrap/cache \
    /tmp/laravel-logs \
    # Set up log symlink
    && ln -sf /tmp/laravel-logs /var/www/storage/logs

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
COPY docker/fix-assets.php /var/www/public/fix-assets.php

# Copy composer files first (for better cache utilization)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader

# Copy package.json files
COPY package.json package-lock.json ./
COPY vite.config.js* ./

# Install JS dependencies (for production builds only, no dev dependencies)
RUN npm ci --omit=dev

# Copy the rest of the application code
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Build frontend assets
RUN npm run build

# Set permissions
RUN chown -R ${USER}:${GROUP} /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache \
    && chmod -R 755 /tmp/laravel-logs \
    && chmod +x /entrypoint.sh

# Expose port
EXPOSE 4004

# Set entrypoint and command
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 