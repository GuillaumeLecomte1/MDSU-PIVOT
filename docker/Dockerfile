FROM php:8.2-apache as builder

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create empty index.html to prevent Vite build error
RUN mkdir -p /tmp/vite-app && echo "<html><body></body></html>" > /tmp/vite-app/index.html

# Set working directory
WORKDIR /var/www

# Copy composer.json and composer.lock
COPY composer.json composer.lock ./

# Install composer dependencies
RUN composer install --no-scripts --no-autoloader

# Copy application code
COPY . .

# Generate optimized composer autoload files
RUN composer dump-autoload --optimize

# Install NPM dependencies and build assets
RUN npm install && \
    mkdir -p public/build public/assets && \
    # Ensure index.html exists for Vite
    cp /tmp/vite-app/index.html public/index.html && \
    NODE_ENV=production npm run build

# Second stage: minimal runtime image
FROM php:8.2-apache

# Install required system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure Apache
RUN a2enmod rewrite headers
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www

# Copy only the necessary files from the builder stage
COPY --from=builder /var/www/public /var/www/public
COPY --from=builder /var/www/storage /var/www/storage
COPY --from=builder /var/www/bootstrap /var/www/bootstrap
COPY --from=builder /var/www/vendor /var/www/vendor
COPY --from=builder /var/www/app /var/www/app
COPY --from=builder /var/www/config /var/www/config
COPY --from=builder /var/www/resources /var/www/resources
COPY --from=builder /var/www/routes /var/www/routes
COPY --from=builder /var/www/database /var/www/database
COPY --from=builder /var/www/artisan /var/www/artisan
COPY --from=builder /var/www/.env.example /var/www/.env.example

# Copy entrypoint and asset fix scripts
COPY docker/entrypoint.sh /entrypoint.sh
COPY docker/fix-assets.php /var/www/public/fix-assets.php

# Fix permissions
RUN chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www/storage && \
    chmod +x /entrypoint.sh

# Configure PHP
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

# Expose port 80
EXPOSE 80

# Set entrypoint
ENTRYPOINT ["/entrypoint.sh"]

# Start Apache in foreground
CMD ["apache2-foreground"] 