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

# Ensure we're in production mode for the build
ENV NODE_ENV=production

# Run the build with explicit output
RUN echo "Building Vite assets..." && \
    npm run build && \
    echo "Build completed. Checking manifest..." && \
    if [ -f "public/build/.vite/manifest.json" ]; then \
        echo "✅ Manifest found at public/build/.vite/manifest.json"; \
        cat public/build/.vite/manifest.json | head -n 10; \
        mkdir -p public/build; \
        cp public/build/.vite/manifest.json public/build/manifest.json; \
        echo "✅ Copied manifest to public/build/manifest.json"; \
    else \
        echo "❌ Manifest NOT found at public/build/.vite/manifest.json"; \
        find public -type f | grep -i manifest; \
        exit 1; \
    fi

# PHP build stage
FROM php-base AS php-build
WORKDIR /var/www

# Copy application code first
COPY . /var/www/

# Copy the build directory from node-build
COPY --from=node-build /var/www/public/build /var/www/public/build

# Verify the manifest is copied
RUN if [ -f "public/build/manifest.json" ]; then \
        echo "✅ Manifest found at public/build/manifest.json in php-build stage"; \
    else \
        echo "❌ Manifest NOT found at public/build/manifest.json in php-build stage"; \
        if [ -f "public/build/.vite/manifest.json" ]; then \
            echo "✅ Found manifest at public/build/.vite/manifest.json, copying..."; \
            cp public/build/.vite/manifest.json public/build/manifest.json; \
        else \
            find public -type f | grep -i manifest; \
            exit 1; \
        fi \
    fi

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chown -R www-data:www-data /var/www/public/build
RUN chown -R www-data:www-data /var/www/public

# Final stage
FROM php-base
ARG PORT=4004

# Copy application code
COPY --from=php-build --chown=www-data:www-data /var/www /var/www

# Verify the manifest exists in the final stage
RUN if [ -f "/var/www/public/build/manifest.json" ]; then \
        echo "✅ Manifest found at /var/www/public/build/manifest.json in final stage"; \
    else \
        echo "❌ Manifest NOT found at /var/www/public/build/manifest.json in final stage"; \
        if [ -f "/var/www/public/build/.vite/manifest.json" ]; then \
            echo "✅ Found manifest at /var/www/public/build/.vite/manifest.json, copying..."; \
            cp /var/www/public/build/.vite/manifest.json /var/www/public/build/manifest.json; \
        else \
            find /var/www/public -type f | grep -i manifest; \
            exit 1; \
        fi \
    fi

# Set proper permissions for all public files
RUN find /var/www/public -type d -exec chmod 755 {} \;
RUN find /var/www/public -type f -exec chmod 644 {} \;
RUN chown -R www-data:www-data /var/www/public

# Configure Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN sed -i "s/listen 80/listen ${PORT}/g" /etc/nginx/sites-available/default

# Configure PHP-FPM
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Configure Supervisor
# Créer un fichier supervisord.conf de base si nécessaire
RUN mkdir -p /etc/supervisor/conf.d
RUN echo "[supervisord]\nnodaemon=true\n\n[program:nginx]\ncommand=/usr/sbin/nginx -g 'daemon off;'\nautostart=true\nautorestart=true\npriority=10\n\n[program:php-fpm]\ncommand=/usr/local/sbin/php-fpm -F\nautostart=true\nautorestart=true\npriority=5" > /etc/supervisor/conf.d/supervisord.conf

# Copy and make the entrypoint script executable
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose port
EXPOSE ${PORT}

# Start services with entrypoint script
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 