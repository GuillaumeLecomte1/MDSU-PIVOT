FROM php:8.2.15-fpm AS base

# Install system dependencies
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
    procps \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get Composer
COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Node.js build stage
FROM node:18.19.1-bullseye AS builder
WORKDIR /var/www

COPY package*.json ./
RUN npm install

COPY . .
ENV NODE_ENV=production
RUN npm run build

# Final stage
FROM base AS pivot-app

# Copy application code
COPY . /var/www/
COPY --from=builder /var/www/public/build /var/www/public/build

# Ensure the storage directory is writable
RUN mkdir -p /var/www/storage/app/public \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs

# Create placeholder image if it doesn't exist
RUN if [ ! -f /var/www/public/images/placeholder.jpg ]; then \
    cp -f /var/www/public/images/logo.svg /var/www/public/images/placeholder.jpg || \
    echo "Placeholder image creation failed, but continuing build"; \
    fi

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Create symbolic link for storage
RUN php artisan storage:link || echo "Storage link creation failed, but continuing build"

# Configure Nginx and PHP-FPM
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Verify that index.php exists
RUN ls -la /var/www/public/

# Set permissions
RUN chown -R www-data:www-data /var/www && \
    find /var/www/storage -type d -exec chmod 775 {} \; && \
    find /var/www/storage -type f -exec chmod 664 {} \; && \
    find /var/www/public -type d -exec chmod 755 {} \; && \
    find /var/www/public -type f -exec chmod 644 {} \;

# Configure port
ARG PORT=4004
RUN sed -i "s/listen 4004/listen ${PORT}/g" /etc/nginx/sites-available/default
EXPOSE ${PORT}

# Start services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 