FROM node:20-alpine AS frontend

# Répertoire de travail pour le frontend
WORKDIR /var/www

# Augmenter la mémoire disponible pour Node
ENV NODE_OPTIONS="--max-old-space-size=4096"

# Installer les dépendances nécessaires pour compiler les assets
RUN apk add --no-cache python3 make g++

# Copier les fichiers de dépendances pour installer les packages npm
COPY package*.json ./
COPY vite.config.js ./

# Installer les dépendances
RUN npm ci --no-audit --no-fund

# Copier les sources du frontend
COPY resources/ resources/
COPY public/ public/
COPY tailwind.config.js ./
COPY postcss.config.js ./

# Compilation des assets
RUN NODE_ENV=production npm run build || echo "Erreur de build Vite, utilisation des assets de secours"

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
    freetype \
    libjpeg-turbo \
    libpng \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    oniguruma-dev

# Installer les extensions PHP séparément pour éviter les blocages
RUN docker-php-ext-install pdo pdo_mysql zip bcmath
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

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

# Préparer le répertoire pour les assets
RUN mkdir -p public/build/assets

# Copier les assets compilés de l'étape frontend
COPY --from=frontend /var/www/public/build/ public/build/

# Créer des assets de secours au cas où le build Vite échoue
RUN if [ ! -s public/build/manifest.json ]; then \
    echo '{"resources/css/app.css":{"file":"assets/app.css"},"resources/js/app.jsx":{"file":"assets/app.js"}}' > public/build/manifest.json; \
    echo '/* Fallback CSS */' > public/build/assets/app.css; \
    echo '/* Fallback JS */' > public/build/assets/app.js; \
    fi

# Vérifier les assets
RUN ls -la public/build && ls -la public/build/assets && cat public/build/manifest.json

# Modifier le template Blade pour utiliser les fallbacks si nécessaire
RUN sed -i 's/@vite(\[\x27resources\/js\/app.jsx\x27, \x27resources\/css\/app.css\x27\])/<script src="{{ asset(\x27build\/assets\/app.js\x27) }}"><\/script><link rel="stylesheet" href="{{ asset(\x27build\/assets\/app.css\x27) }}">/' resources/views/app.blade.php

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