FROM php:8.2-fpm AS base

# Install system dependencies
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
    procps \
    iputils-ping \
    net-tools \
    dnsutils \
    telnet \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure intl \
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

# Get Composer
COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Prepare directory structure
RUN mkdir -p /var/www/storage/app/public \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /tmp/laravel-logs \
    /var/www/bootstrap/cache \
    /var/www/public/images \
    /var/www/public/build/assets \
    /var/www/docker \
    /var/www/resources/views/components

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /var/www/docker/entrypoint.sh
RUN chmod +x /var/www/docker/entrypoint.sh

# Create diagnostic files
RUN echo '<?php phpinfo();' > /var/www/public/info.php \
    && echo '<?php echo json_encode(["status" => "ok", "timestamp" => time()]);' > /var/www/public/health-check.php

# Copy dependencies first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-autoloader \
    --no-scripts \
    --no-dev \
    --no-interaction \
    --prefer-dist

# Copy application code
COPY --chown=www-data:www-data . .

# Configure logs
RUN rm -rf /var/www/storage/logs && \
    ln -sf /tmp/laravel-logs /var/www/storage/logs

# Copy environment file
COPY docker/env.production /var/www/.env

# Set permissions
RUN chmod -R 777 /var/www/storage \
    && chmod -R 777 /var/www/bootstrap/cache \
    && chmod -R 777 /tmp/laravel-logs \
    && chown -R www-data:www-data /var/www \
    && chown -R www-data:www-data /tmp/laravel-logs \
    && touch /tmp/laravel-logs/laravel.log \
    && chmod 666 /tmp/laravel-logs/laravel.log

# Generate optimized autoloader
RUN COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --no-dev --optimize

# Install Breeze for Blade components
RUN COMPOSER_ALLOW_SUPERUSER=1 composer require --no-interaction laravel/breeze

# Create Blade components manually
RUN echo '<label {{ $attributes->merge(["class" => "block font-medium text-sm text-gray-700"]) }}>{{ $slot }}</label>' > /var/www/resources/views/components/input-label.blade.php && \
    echo '<input {{ $attributes->merge(["class" => "border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"]) }}>' > /var/www/resources/views/components/text-input.blade.php && \
    echo '<input type="checkbox" {!! $attributes->merge(["class" => "rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"]) !!}>' > /var/www/resources/views/components/checkbox.blade.php && \
    echo '<button {{ $attributes->merge(["type" => "submit", "class" => "inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"]) }}>{{ $slot }}</button>' > /var/www/resources/views/components/primary-button.blade.php && \
    echo '<div {{ $attributes->merge(["class" => "text-sm text-red-600 space-y-1"]) }}>{{ $slot }}</div>' > /var/www/resources/views/components/input-error.blade.php && \
    echo '<div {{ $attributes->merge(["class" => "p-4 text-sm text-gray-600"]) }}>{{ $slot }}</div>' > /var/www/resources/views/components/auth-session-status.blade.php

# Cleanup
RUN rm -rf /var/www/node_modules \
    /var/www/.git \
    /var/www/.github \
    && find /var/www -name ".git*" -type f -delete \
    && composer clear-cache

# Expose port
EXPOSE 4004

# Run entrypoint
ENTRYPOINT ["/var/www/docker/entrypoint.sh"] 