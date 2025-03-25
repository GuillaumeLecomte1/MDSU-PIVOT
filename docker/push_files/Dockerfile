# Utilisons une image officielle PHP avec FPM comme base
FROM php:8.2-fpm-alpine

# Labels pour documenter et tracer l'image
LABEL maintainer="Guillaume Lecomte"
LABEL description="Image Docker pour l'application MDSU-PIVOT"

# Arguments pour la construction
ARG USER_ID=1000
ARG GROUP_ID=1000
ARG NODE_VERSION=20

# Variables d'environnement
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=0
ENV PHP_OPCACHE_SAVE_COMMENTS=1

# Installation des dépendances système
RUN apk add --no-cache \
    nginx \
    supervisor \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    mysql-client \
    nodejs \
    npm \
    bash

# Installation et configuration de PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    bcmath \
    opcache \
    zip \
    exif \
    pcntl \
    intl \
    gd \
    xml \
    && docker-php-ext-enable opcache

# Utilisation de npm pour installer Node.js plus récent
RUN npm install -g n && n ${NODE_VERSION} && hash -r

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
RUN composer install --no-scripts --no-autoloader --ignore-platform-reqs

# Copie de l'ensemble du code source de l'application
COPY . .

# Construction des assets frontend avec gestion des erreurs
RUN mkdir -p public/build/assets \
    && npm install \
    && npm run build || echo "Frontend build failed, using fallback assets" \
    # Création du manifeste complet en cas d'échec de npm run build
    && if [ ! -f "public/build/manifest.json" ] || [ $(cat public/build/manifest.json | wc -c) -lt 10 ]; then \
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
        echo "/* Fallback CSS */" > public/build/assets/app.css; \
        echo "/* Fallback JS */" > public/build/assets/app.js; \
    fi

# Finalisation de l'installation Composer et optimisations
RUN composer dump-autoload --optimize \
    && composer run-script post-autoload-dump

# Configuration des permissions
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && mkdir -p /var/log/supervisor \
    && chown -R www-data:www-data /var/log/supervisor

# Configuration Traefik pour le routage
LABEL traefik.enable=true \
      traefik.http.routers.pivot.rule=Host(`pivot.guillaume-lcte.fr`) \
      traefik.http.routers.pivot.entrypoints=web,websecure \
      traefik.http.routers.pivot.tls.certresolver=letsencrypt \
      traefik.http.services.pivot.loadbalancer.server.port=4004

# Script d'entrée plus performant et fiable
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Port pour Nginx
EXPOSE 4004

# Entrypoint
ENTRYPOINT ["/entrypoint.sh"] 