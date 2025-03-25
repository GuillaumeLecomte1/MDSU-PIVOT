# Utilisons une image officielle PHP avec FPM comme base
FROM php:8.2-fpm-alpine

# Labels for identification
LABEL maintainer="Guillaume Lecomte"
LABEL description="Production-ready Docker image for Laravel with Inertia.js and React"

# Arguments
ARG NODE_VERSION=20
ARG PORT=4004
ARG UID=1000
ARG GID=1000

# Environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=0
ENV PHP_OPCACHE_SAVE_COMMENTS=1
ENV VITE_MANIFEST_PATH=/var/www/public/build/manifest.json
ENV NGINX_PORT=${PORT}
ENV DISABLE_FUNCTIONS="proc_open"

# Installation des dépendances système
RUN apk add --no-cache \
    # Nginx and Supervisor
    nginx \
    supervisor \
    # Utilities
    zip \
    unzip \
    7zip \
    git \
    curl \
    bash \
    shadow \
    mysql-client \
    # PHP extensions dependencies
    libpng-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    freetype-dev \
    libjpeg-turbo-dev

# Fix php-fpm user and set proper PHP configuration
RUN usermod -u ${UID} www-data && \
    groupmod -g ${GID} www-data && \
    # Enable needed PHP functions
    echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/docker-php-memory.ini

# Installation et configuration de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    bcmath \
    opcache \
    zip \
    exif \
    pcntl \
    intl \
    gd \
    xml && \
    docker-php-ext-enable opcache

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration d'Nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Configuration de PHP pour la production
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Configuration de Supervisord
COPY docker/supervisord.conf /etc/supervisord.conf

# Préparation du dossier de l'application
WORKDIR /var/www
RUN mkdir -p /var/www && \
    chown -R www-data:www-data /var/www

# Create required directories first
RUN mkdir -p bootstrap/cache storage/framework/{sessions,views,cache} && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

# Copie des fichiers nécessaires pour Composer
COPY composer.json composer.lock ./

# Installation des dépendances PHP sans scripts
RUN php -d memory_limit=-1 /usr/bin/composer install --no-scripts --no-autoloader --ignore-platform-reqs

# Copy pre-built Vite assets
COPY public/build /var/www/public/build

# Copie de l'ensemble du code source de l'application
COPY . .

# Finalize Composer installation
RUN php -d memory_limit=-1 /usr/bin/composer dump-autoload --optimize || true

# Ensure Vite manifest has the proper format with 'src' field
RUN if [ ! -f "public/build/manifest.json" ] || ! grep -q "\"src\":" public/build/manifest.json 2>/dev/null; then \
    echo "Creating proper Vite manifest with src field"; \
    mkdir -p public/build/assets; \
    echo '{ \
        "resources/css/app.css": { \
            "file": "assets/app.css", \
            "src": "resources/css/app.css", \
            "isEntry": true \
        }, \
        "resources/js/app.jsx": { \
            "file": "assets/app.js", \
            "src": "resources/js/app.jsx", \
            "isEntry": true \
        } \
    }' > public/build/manifest.json; \
    # Create fallback assets if needed
    if [ ! -s public/build/assets/app.css ]; then \
        echo "/* Fallback CSS */" > public/build/assets/app.css; \
    fi; \
    if [ ! -s public/build/assets/app.js ]; then \
        echo "/* Fallback JS */" > public/build/assets/app.js; \
    fi; \
fi

# Set proper permissions
RUN chown -R www-data:www-data /var/www && \
    find storage bootstrap/cache -type d -exec chmod 775 {} \; && \
    find storage bootstrap/cache -type f -exec chmod 664 {} \;

# Prepare log directory for supervisor
RUN mkdir -p /var/log/supervisor && \
    chown -R www-data:www-data /var/log/supervisor

# Configuration Traefik pour le routage
LABEL traefik.enable=true \
      traefik.http.routers.pivot.rule=Host(`pivot.guillaume-lcte.fr`) \
      traefik.http.routers.pivot.entrypoints=web,websecure \
      traefik.http.routers.pivot.tls.certresolver=letsencrypt \
      traefik.http.services.pivot.loadbalancer.server.port=${PORT}

# Copy the entrypoint script
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Port pour Nginx
EXPOSE ${PORT}

# Set up healthcheck
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost:${PORT} || exit 1

# Entrypoint
ENTRYPOINT ["/entrypoint.sh"] 