FROM node:20-alpine AS frontend

# Répertoire de travail pour le frontend
WORKDIR /var/www

# Copier les fichiers de dépendances pour installer les packages npm
COPY package*.json ./
COPY vite.config.js ./

# Installer les dépendances
RUN npm ci

# Copier les sources du frontend
COPY resources/ resources/
COPY public/ public/

# Compilation des assets
RUN npm run build

# Image PHP pour l'application Laravel
FROM php:8.2-fpm-alpine AS backend

# Installer les dépendances système nécessaires
RUN apk --no-cache add \
    nginx \
    libzip-dev \
    zip \
    unzip \
    git \
    supervisor \
    mysql-client \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev

# Installer les extensions PHP requises
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) pdo pdo_mysql zip bcmath gd

# Configurer Nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www

# Optimiser PHP pour la production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    sed -i 's/memory_limit = .*/memory_limit = 256M/' "$PHP_INI_DIR/php.ini" && \
    sed -i 's/max_execution_time = .*/max_execution_time = 60/' "$PHP_INI_DIR/php.ini" && \
    sed -i 's/post_max_size = .*/post_max_size = 100M/' "$PHP_INI_DIR/php.ini" && \
    sed -i 's/upload_max_filesize = .*/upload_max_filesize = 100M/' "$PHP_INI_DIR/php.ini"

# Configurer supervisor pour gérer PHP-FPM et Nginx
COPY docker/supervisord.conf /etc/supervisord.conf

# Copier les fichiers composer et installer les dépendances
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-dev

# Copier le code de l'application
COPY . .

# Copier les assets compilés depuis l'étape frontend
COPY --from=frontend /var/www/public/build/ ./public/build/

# Finaliser l'installation de Composer
RUN composer dump-autoload --optimize

# Créer les répertoires nécessaires et définir les permissions
RUN mkdir -p storage/framework/{sessions,views,cache} && \
    mkdir -p storage/logs && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

# Créer le script d'entrée
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Créer le script entrypoint s'il n'existe pas
RUN if [ ! -f /entrypoint.sh ]; then \
    echo '#!/bin/sh' > /entrypoint.sh && \
    echo 'set -e' >> /entrypoint.sh && \
    echo 'cd /var/www' >> /entrypoint.sh && \
    echo 'php artisan storage:link --force' >> /entrypoint.sh && \
    echo 'php artisan config:cache' >> /entrypoint.sh && \
    echo 'php artisan route:cache' >> /entrypoint.sh && \
    echo 'php artisan view:cache' >> /entrypoint.sh && \
    echo 'php artisan optimize' >> /entrypoint.sh && \
    echo 'supervisord -c /etc/supervisord.conf' >> /entrypoint.sh && \
    chmod +x /entrypoint.sh; \
    fi

# Configuration Traefik pour le routage
LABEL traefik.enable=true \
      traefik.http.routers.pivot.rule=Host(`pivot.guillaume-lcte.fr`) \
      traefik.http.routers.pivot.entrypoints=web,websecure \
      traefik.http.routers.pivot.tls.certresolver=letsencrypt \
      traefik.http.services.pivot.loadbalancer.server.port=4004

# Exposer le port 4004
EXPOSE 4004

# Définir le point d'entrée
ENTRYPOINT ["/entrypoint.sh"] 