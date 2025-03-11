FROM php:8.2-fpm

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
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get Composer
COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy only necessary files first
COPY composer.json composer.lock package.json package-lock.json vite.config.js /var/www/

# Create necessary directories
RUN mkdir -p /var/www/storage/app/public \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/public/images \
    && mkdir -p /var/www/resources \
    && mkdir -p /var/www/app \
    && mkdir -p /var/www/config \
    && mkdir -p /var/www/routes \
    && mkdir -p /var/www/bootstrap \
    && mkdir -p /var/www/database

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
RUN npm install

# Copy the rest of the application code
COPY app /var/www/app
COPY bootstrap /var/www/bootstrap
COPY config /var/www/config
COPY database /var/www/database
COPY public /var/www/public
COPY resources /var/www/resources
COPY routes /var/www/routes
COPY artisan /var/www/artisan

# Build assets
RUN npm run build

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Configure supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www && \
    find /var/www/storage -type d -exec chmod 775 {} \; && \
    find /var/www/storage -type f -exec chmod 664 {} \; && \
    find /var/www/public -type d -exec chmod 755 {} \; && \
    find /var/www/public -type f -exec chmod 644 {} \;

# Expose port
EXPOSE 4004

# Start services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 