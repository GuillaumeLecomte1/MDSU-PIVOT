FROM php:8.2-fpm-alpine AS php-base

# Install system dependencies and PHP extensions in a single layer
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    oniguruma-dev \
    libxml2-dev \
    # Install PHP extensions
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Node.js build stage
FROM node:18-alpine AS node-build
WORKDIR /var/www
COPY package*.json ./
RUN npm ci --quiet
COPY . .

# Ensure we're in production mode for the build
ENV NODE_ENV=production

# Run the build with explicit output
RUN echo "Building Vite assets..." && \
    npm run build && \
    echo "Build completed. Checking manifest..." && \
    if [ -f "public/build/.vite/manifest.json" ]; then \
        echo "✅ Manifest found at public/build/.vite/manifest.json"; \
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

# Install PHP dependencies and set permissions in a single layer
RUN composer install --optimize-autoloader --no-dev && \
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www/public

# Final stage
FROM php-base
ARG PORT=4004
ENV PORT=${PORT}

# Copy application code
COPY --from=php-build --chown=www-data:www-data /var/www /var/www

# Set proper permissions and configure files in a single layer
RUN find /var/www/public -type d -exec chmod 755 {} \; && \
    find /var/www/public -type f -exec chmod 644 {} \; && \
    chown -R www-data:www-data /var/www/public && \
    mkdir -p /etc/supervisor/conf.d && \
    mkdir -p /var/www/docker/scripts

# Create nginx config
RUN mkdir -p /run/nginx
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
RUN sed -i "s/listen 80/listen ${PORT}/g" /etc/nginx/http.d/default.conf

# Create PHP-FPM config
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Create required scripts
RUN echo '#!/bin/sh\nif [ -f "/var/www/public/index.php" ] && [ -f "/usr/local/etc/php-fpm.d/www.conf" ]; then\n  exit 0\nelse\n  exit 1\nfi' > /var/www/docker/scripts/healthcheck.sh && \
    chmod +x /var/www/docker/scripts/healthcheck.sh

# Create supervisor config
RUN echo '[supervisord]\nnodaemon=true\nuser=root\nloglevel=info\nlogfile=/var/log/supervisord.log\n\n[program:nginx]\ncommand=/usr/sbin/nginx -g "daemon off;"\nautostart=true\nautorestart=true\nstdout_logfile=/dev/stdout\nstdout_logfile_maxbytes=0\nstderr_logfile=/dev/stderr\nstderr_logfile_maxbytes=0\n\n[program:php-fpm]\ncommand=/usr/local/sbin/php-fpm -F\nautostart=true\nautorestart=true\nstdout_logfile=/dev/stdout\nstdout_logfile_maxbytes=0\nstderr_logfile=/dev/stderr\nstderr_logfile_maxbytes=0' > /etc/supervisor/conf.d/supervisord.conf

# Create entrypoint script
RUN echo '#!/bin/sh\nset -e\n\n# Create storage directories\nmkdir -p /var/www/storage/app/public\nmkdir -p /var/www/storage/framework/sessions\nmkdir -p /var/www/storage/framework/views\nmkdir -p /var/www/storage/framework/cache\nmkdir -p /var/www/storage/logs\n\n# Ensure correct permissions\nchown -R www-data:www-data /var/www/storage\nchown -R www-data:www-data /var/www/bootstrap/cache\n\n# Create storage link if not exists\nif [ ! -L /var/www/public/storage ]; then\n  ln -sf /var/www/storage/app/public /var/www/public/storage\nfi\n\n# Optimize Laravel\nphp /var/www/artisan config:cache\nphp /var/www/artisan route:cache\nphp /var/www/artisan view:cache\n\n# Start services\nexec "$@"' > /entrypoint.sh && \
    chmod +x /entrypoint.sh

# Add health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 CMD /var/www/docker/scripts/healthcheck.sh

# Expose port
EXPOSE ${PORT}

# Start services with entrypoint script
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 