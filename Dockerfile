FROM php:8.2-fpm AS php-base

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
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Node.js build stage
FROM node:18 AS node-build
WORKDIR /var/www
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# PHP build stage
FROM php-base AS php-build
COPY . /var/www/
COPY --from=node-build /var/www/public/build /var/www/public/build
COPY --from=node-build /var/www/dist /var/www/dist

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Final stage
FROM php-base
ARG PORT=80

# Copy application code
COPY --from=php-build --chown=www-data:www-data /var/www /var/www

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN sed -i "s/listen 80/listen ${PORT}/g" /etc/nginx/sites-available/default

# Configure PHP-FPM
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Configure Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port
EXPOSE ${PORT}

# Start services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 