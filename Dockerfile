# Utiliser l'image officielle PHP avec FPM comme base
FROM php:8.2-fpm-alpine

# Labels pour l'identification
LABEL maintainer="Guillaume Lecomte"
LABEL description="Production-ready Docker image for Laravel with Inertia.js and React"

# Arguments
ARG NODE_VERSION=20
ARG PORT=4004
ARG UID=1000
ARG GID=1000

# Variables d'environnement
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=0
ENV PHP_OPCACHE_SAVE_COMMENTS=1
ENV VITE_MANIFEST_PATH=/var/www/public/build/manifest.json
ENV NGINX_PORT=${PORT}
ENV NODE_OPTIONS="--max-old-space-size=4096"

# Installation des dépendances système en une seule commande pour réduire les couches
RUN apk add --no-cache \
    nginx supervisor zip unzip 7zip git curl bash shadow mysql-client \
    libpng-dev libzip-dev icu-dev oniguruma-dev libxml2-dev freetype-dev libjpeg-turbo-dev \
    && usermod -u ${UID} www-data \
    && groupmod -g ${GID} www-data \
    && echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/docker-php-memory.ini \
    && echo "disable_functions = " > /usr/local/etc/php/conf.d/docker-php-enable-functions.ini

# Installation et configuration de PHP en une seule étape pour optimiser le cache Docker
# Utilise --disable-opcache-jit pour éviter la compilation lente du JIT
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
       pdo pdo_mysql bcmath zip exif pcntl intl gd xml \
    && docker-php-ext-enable opcache \
    && echo "opcache.jit=off" > /usr/local/etc/php/conf.d/disable-jit.ini \
    && echo "opcache.jit_buffer_size=0" >> /usr/local/etc/php/conf.d/disable-jit.ini

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration d'Nginx et PHP
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisord.conf

# Préparation du dossier de l'application
WORKDIR /var/www
RUN mkdir -p /var/www \
    && chown -R www-data:www-data /var/www \
    && mkdir -p bootstrap/cache storage/framework/{sessions,views,cache} \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Installation de Node.js
RUN apk add --no-cache nodejs npm \
    && mkdir -p /.npm \
    && chown -R www-data:www-data /.npm

# Copie des fichiers pour Composer
COPY composer.json composer.lock ./

# Installation des dépendances PHP
RUN php -d memory_limit=-1 /usr/bin/composer install --no-scripts --no-autoloader --ignore-platform-reqs

# Copie de l'ensemble du code source
COPY . .

# Installation des dépendances Node.js
RUN npm install --no-audit --no-fund && \
    npm install lodash --save

# Construction des assets avec Vite avec une limite de mémoire augmentée
RUN NODE_OPTIONS=--max-old-space-size=4096 \
    npm run build || echo "Vite build may have failed, will attempt to fix during startup"

# Correctif pour la classe Vite de Laravel pour gérer le champ 'src' manquant
RUN set -e && \
    VITE_PHP_PATH="/var/www/vendor/laravel/framework/src/Illuminate/Foundation/Vite.php" && \
    if [ -f "$VITE_PHP_PATH" ]; then \
        echo "Patching Vite.php to handle missing 'src' field..." && \
        cp "$VITE_PHP_PATH" "$VITE_PHP_PATH.backup" && \
        sed -i 's/$path = $chunk\['\''src'\''\];/if (isset($chunk['\''src'\''])) { $path = $chunk['\''src'\'']; } else { $path = $file; }/g' "$VITE_PHP_PATH"; \
    fi

# Correction du problème de duplicate Larastan
RUN if [ -d "vendor/nunomaduro/larastan" ] && [ -d "vendor/larastan/larastan" ]; then \
    echo "Removing duplicate Larastan package..." && \
    rm -rf vendor/nunomaduro/larastan; \
fi

# Finalisation de l'installation Composer
RUN php -d memory_limit=-1 /usr/bin/composer dump-autoload --optimize

# Configuration des permissions
RUN chown -R www-data:www-data /var/www && \
    find storage bootstrap/cache -type d -exec chmod 775 {} \; && \
    find storage bootstrap/cache -type f -exec chmod 664 {} \; && \
    mkdir -p /var/log/supervisor && \
    chown -R www-data:www-data /var/log/supervisor

# Configuration Traefik pour le routage
LABEL traefik.enable=true \
      traefik.http.routers.pivot.rule=Host(`pivot.guillaume-lcte.fr`) \
      traefik.http.routers.pivot.entrypoints=web,websecure \
      traefik.http.routers.pivot.tls.certresolver=letsencrypt \
      traefik.http.services.pivot.loadbalancer.server.port=${PORT}

# Copie du script d'entrée
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Exposition du port pour Nginx
EXPOSE ${PORT}

# Configuration du healthcheck
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost:${PORT} || exit 1

# Point d'entrée
ENTRYPOINT ["/entrypoint.sh"] 