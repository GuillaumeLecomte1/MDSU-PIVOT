# Image de build pour les dépendances PHP
FROM composer:2.5 as composer_build
WORKDIR /app
COPY composer.* ./
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

# Node pour compiler les assets React
FROM node:18-alpine as node_build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
COPY --from=composer_build /app/vendor /app/vendor
RUN npm run build

# Image PHP finale pour l'exécution
FROM php:8.1-fpm-alpine

# Installation des extensions PHP nécessaires
RUN apk add --no-cache \
    freetype \
    libpng \
    libjpeg-turbo \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mbstring zip exif pcntl bcmath intl \
    && apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev

# Installation de Nginx et Supervisor
RUN apk add --no-cache nginx supervisor

# Création du dossier pour les sockets PHP-FPM et Nginx
RUN mkdir -p /var/run/php-fpm

# Copie des fichiers de configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Copie de l'application
WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --from=composer_build --chown=www-data:www-data /app/vendor ./vendor
COPY --from=node_build --chown=www-data:www-data /app/public/build ./public/build

# Installation complète de Composer et préparation de l'application
RUN composer install --no-dev --optimize-autoloader \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Exposition du port 4004
EXPOSE 4004

# Démarrage des services avec notre script
CMD ["/start.sh"] 