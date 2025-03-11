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
    imagemagick \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer with specific version
COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Node.js build stage avec version spécifique
FROM node:18.19.1-bullseye AS node-build
WORKDIR /var/www

# Add retry mechanism for npm
RUN npm config set fetch-retries 5 \
    && npm config set fetch-retry-mintimeout 20000 \
    && npm config set fetch-retry-maxtimeout 120000

COPY package*.json ./
RUN npm ci --fetch-retries=5 --fetch-retry-mintimeout=20000 --fetch-retry-maxtimeout=120000
COPY . .

# Ensure we're in production mode for the build
ENV NODE_ENV=production

# Run the build and handle manifest
RUN echo "Building Vite assets..." && \
    npm run build && \
    echo "Build completed. Checking manifest..." && \
    mkdir -p public/build && \
    if [ -f "public/build/manifest.json" ]; then \
        echo "✅ Manifest found at public/build/manifest.json"; \
        cat public/build/manifest.json | head -n 10; \
    elif [ -f "public/build/.vite/manifest.json" ]; then \
        echo "✅ Found manifest in .vite directory, copying..."; \
        cp public/build/.vite/manifest.json public/build/manifest.json; \
        cat public/build/manifest.json | head -n 10; \
    else \
        echo "❌ Manifest not found in any location"; \
        find public -type f | grep -i manifest; \
        exit 1; \
    fi

# Ensure proper permissions and directories
RUN mkdir -p public/build/assets/images && \
    chmod -R 755 public/build && \
    chown -R node:node public/build

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
FROM php-base AS pivot-app
ARG PORT=4004

# Copy application code
COPY --from=php-build --chown=www-data:www-data /var/www /var/www

# Create necessary directories first
RUN mkdir -p /var/www/public/build/assets/images \
    && mkdir -p /var/www/public/images \
    && mkdir -p /var/www/public/storage/imagesAccueil \
    && mkdir -p /var/www/storage/app/public/imagesAccueil \
    && chown -R www-data:www-data /var/www/public/build /var/www/public/images /var/www/public/storage /var/www/storage/app/public

# Create placeholder image directly
RUN apt-get update && apt-get install -y imagemagick \
    && convert -size 300x300 canvas:lightgray -font Arial -pointsize 20 -gravity center -annotate 0 "Image non disponible" /var/www/public/images/placeholder.jpg \
    && cp /var/www/public/images/placeholder.jpg /var/www/public/build/images/placeholder.jpg \
    && chmod 644 /var/www/public/images/placeholder.jpg \
    && chmod 644 /var/www/public/build/images/placeholder.jpg \
    && chown www-data:www-data /var/www/public/images/placeholder.jpg \
    && chown www-data:www-data /var/www/public/build/images/placeholder.jpg

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
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN sed -i "s/listen 4004/listen ${PORT}/g" /etc/nginx/sites-available/default

# Configure PHP-FPM
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Configure Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy scripts
COPY docker/check-vite-manifest.sh /var/www/docker/check-vite-manifest.sh
COPY docker/fix-vite-issues.php /var/www/docker/fix-vite-issues.php
COPY docker/healthcheck.sh /var/www/docker/healthcheck.sh
COPY docker/fix-static-files.sh /var/www/docker/fix-static-files.sh
COPY docker/fix-manifest.sh /var/www/docker/fix-manifest.sh
COPY docker/create-placeholder.sh /var/www/docker/create-placeholder.sh
COPY docker/fix-images.sh /var/www/docker/fix-images.sh
COPY docker/fix-encoding.sh /var/www/docker/fix-encoding.sh
RUN chmod +x /var/www/docker/*.sh

# Run the fix scripts with proper error handling
RUN /var/www/docker/fix-encoding.sh || true
RUN cd /var/www && php docker/fix-vite-issues.php || true
RUN /var/www/docker/fix-static-files.sh || true
RUN /var/www/docker/fix-manifest.sh || true
RUN /var/www/docker/fix-images.sh || true

# Create storage symlink if it doesn't exist
RUN if [ ! -L "/var/www/public/storage" ] && [ -d "/var/www/storage/app/public" ]; then \
    ln -sf /var/www/storage/app/public /var/www/public/storage; \
fi

# Expose port
EXPOSE ${PORT}

# Healthcheck to ensure the container is running properly
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD /var/www/docker/healthcheck.sh || exit 1

# Start services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 