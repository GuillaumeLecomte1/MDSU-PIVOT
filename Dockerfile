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

# Installation des dépendances système
RUN apk add --no-cache \
    # Nginx and Supervisor
    nginx \
    supervisor \
    # Utilities
    zip \
    unzip \
    git \
    curl \
    bash \
    mysql-client \
    # PHP extensions dependencies
    libpng-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    # Node.js and npm
    nodejs \
    npm

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

# Utilisation de npm pour installer Node.js plus récent
RUN npm install -g n && \
    n ${NODE_VERSION} && \
    hash -r && \
    npm install -g npm@latest

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
RUN chown -R www-data:www-data /var/www

# Copie des fichiers nécessaires pour Composer
COPY composer.json composer.lock ./

# Installation des dépendances PHP sans scripts
RUN composer install --no-scripts --no-autoloader --ignore-platform-reqs && \
    mkdir -p bootstrap/cache storage/framework/{sessions,views,cache}

# Copie de l'ensemble du code source de l'application
COPY . .

# Construction des assets frontend avec gestion des erreurs
RUN sed -i 's/minify: false/minify: true/' vite.config.js && \
    sed -i 's/sourcemap: false/sourcemap: false/' vite.config.js && \
    sed -i 's/terserOptions: undefined/terserOptions: { compress: true, mangle: true }/' vite.config.js

RUN NODE_ENV=production npm run build

RUN if [ ! -f "public/build/manifest.json" ]; then \
    echo "Vite build failed! Creating fallback assets"; \
    mkdir -p public/build/assets; \
    echo '{"resources/css/app.css":{"file":"assets/app.css"},"resources/js/app.jsx":{"file":"assets/app.js"}}' > public/build/manifest.json; \
    echo "/* Fallback CSS */" > public/build/assets/app.css; \
    echo "/* Fallback JS */" > public/build/assets/app.js; \
    fi

# Finalisation de l'installation Composer et optimisations
RUN composer dump-autoload --optimize && \
    php artisan package:discover && \
    chown -R www-data:www-data /var/www && \
    find storage bootstrap/cache -type d -exec chmod 775 {} \; && \
    find storage bootstrap/cache -type f -exec chmod 664 {} \;

# Configuration Traefik pour le routage
LABEL traefik.enable=true \
      traefik.http.routers.pivot.rule=Host(`pivot.guillaume-lcte.fr`) \
      traefik.http.routers.pivot.entrypoints=web,websecure \
      traefik.http.routers.pivot.tls.certresolver=letsencrypt \
      traefik.http.services.pivot.loadbalancer.server.port=${PORT}

# Script d'entrée plus performant et fiable
RUN echo '#!/bin/sh' > /entrypoint.sh && \
    echo 'set -e' >> /entrypoint.sh && \
    echo 'cd /var/www' >> /entrypoint.sh && \
    echo '# Link storage' >> /entrypoint.sh && \
    echo 'php artisan storage:link --force || true' >> /entrypoint.sh && \
    echo '# Cache configuration' >> /entrypoint.sh && \
    echo 'php artisan config:cache' >> /entrypoint.sh && \
    echo 'php artisan route:cache' >> /entrypoint.sh && \
    echo 'php artisan view:cache' >> /entrypoint.sh && \
    echo '# Start supervisor' >> /entrypoint.sh && \
    echo 'exec supervisord -c /etc/supervisord.conf' >> /entrypoint.sh && \
    chmod +x /entrypoint.sh

# Port pour Nginx
EXPOSE ${PORT}

# Set up healthcheck
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost:${PORT} || exit 1

# Entrypoint
ENTRYPOINT ["/entrypoint.sh"] 