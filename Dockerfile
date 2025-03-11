FROM php:8.2.15-fpm AS php-base

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

# Get latest Composer with specific version
COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Node.js build stage
FROM node:18.19.1-bullseye AS node-build
WORKDIR /var/www

# Add retry mechanism for npm
RUN npm config set fetch-retries 5 \
    && npm config set fetch-retry-mintimeout 20000 \
    && npm config set fetch-retry-maxtimeout 120000

COPY package*.json ./
RUN npm ci
COPY . .

# Build assets
ENV NODE_ENV=production
RUN npm run build

# PHP build stage
FROM php-base AS php-build
WORKDIR /var/www

# Copy application code
COPY . /var/www/
COPY --from=node-build /var/www/public/build /var/www/public/build

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public

# Final stage
FROM php-base AS pivot-app
ARG PORT=4004

# Copy application code
COPY --from=php-build --chown=www-data:www-data /var/www /var/www

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN sed -i "s/listen 80/listen ${PORT}/g" /etc/nginx/sites-available/default

# Configure PHP-FPM
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Configure Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set proper permissions
RUN find /var/www/public -type d -exec chmod 755 {} \; && \
    find /var/www/public -type f -exec chmod 644 {} \; && \
    chown -R www-data:www-data /var/www/public

# Expose port
EXPOSE ${PORT}

# Start services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 