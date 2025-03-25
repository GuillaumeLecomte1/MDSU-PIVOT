FROM php:8.2-fpm-alpine AS php-base

# Installer les dépendances système et PHP essentielles uniquement
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    nginx \
    supervisor \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    mysql-client \
    && docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd zip \
    && docker-php-ext-configure intl && docker-php-ext-install intl \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && mkdir -p /run/nginx

# Optimiser la configuration PHP pour Laravel en production
RUN echo "upload_max_filesize = 64M" > $PHP_INI_DIR/conf.d/upload.ini \
    && echo "post_max_size = 64M" >> $PHP_INI_DIR/conf.d/upload.ini \
    && echo "memory_limit = 512M" >> $PHP_INI_DIR/conf.d/upload.ini \
    && echo "max_execution_time = 120" >> $PHP_INI_DIR/conf.d/upload.ini \
    && echo "opcache.enable=1" > $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.enable_cli=1" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> $PHP_INI_DIR/conf.d/opcache.ini

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www

# Étape de build Node.js avec temps d'exécution étendu
FROM node:18-alpine AS node-build
WORKDIR /var/www

# Augmenter la limite de mémoire Node pour éviter les blocages
ENV NODE_OPTIONS="--max-old-space-size=4096"

# Copier uniquement les fichiers nécessaires pour l'installation
COPY package*.json ./
RUN npm ci --quiet --no-audit --no-optional --prefer-offline

# Copier le reste des fichiers source
COPY . .

# Construire les assets avec Vite en mode production avec timeout étendu
ENV NODE_ENV=production \
    VITE_DISABLE_ESLINT=true \
    VITE_DROP_CONSOLE=true
    
RUN npm run build \
    && mkdir -p public/build \
    && if [ -f "public/build/.vite/manifest.json" ]; then \
          cp public/build/.vite/manifest.json public/build/manifest.json; \
       elif [ -f "public/build/manifest.json" ]; then \
          echo "Manifest déjà existant dans public/build"; \
       else \
          echo "Erreur: manifest.json non trouvé"; \
          find public -type f -name "*.json" | sort; \
          exit 1; \
       fi

# Étape de build PHP avec dépendances minimales
FROM php-base AS php-build
WORKDIR /var/www

# Copier le code de l'application
COPY --chown=www-data:www-data . /var/www/

# Copier les assets compilés depuis l'étape node-build
COPY --from=node-build --chown=www-data:www-data /var/www/public/build /var/www/public/build

# Installer les dépendances PHP de production uniquement
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction \
    && mkdir -p storage/app/public \
    && mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache

# Étape finale avec image minimale
FROM php-base
ARG PORT=4004
ENV PORT=${PORT} \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

# Copier le code de l'application depuis l'étape de build PHP
COPY --from=php-build --chown=www-data:www-data /var/www /var/www

# Configurer nginx
RUN echo 'server { \
    listen '${PORT}'; \
    root /var/www/public; \
    index index.php; \
    charset utf-8; \
    client_max_body_size 64M; \
    gzip on; \
    gzip_comp_level 5; \
    gzip_types application/javascript application/json text/css; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ /\.(?!well-known).* { \
        deny all; \
    } \
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ { \
        expires 30d; \
        add_header Cache-Control "public, no-transform"; \
    } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        include fastcgi_params; \
        fastcgi_read_timeout 120; \
    } \
}' > /etc/nginx/http.d/default.conf

# Configurer PHP-FPM de manière simplifiée
RUN echo '[global] \
error_log = /proc/self/fd/2 \
daemonize = no \
[www] \
user = www-data \
group = www-data \
listen = 9000 \
pm = dynamic \
pm.max_children = 20 \
pm.start_servers = 5 \
pm.min_spare_servers = 2 \
pm.max_spare_servers = 10 \
access.log = /proc/self/fd/2 \
clear_env = no \
catch_workers_output = yes \
' > /usr/local/etc/php-fpm.d/www.conf

# Configurer supervisord de manière simplifiée
RUN mkdir -p /etc/supervisor/conf.d \
    && echo '[supervisord] \
nodaemon=true \
user=root \
logfile=/dev/stdout \
logfile_maxbytes=0 \
[program:php-fpm] \
command=php-fpm -F \
stdout_logfile=/dev/stdout \
stdout_logfile_maxbytes=0 \
stderr_logfile=/dev/stderr \
stderr_logfile_maxbytes=0 \
[program:nginx] \
command=nginx -g "daemon off;" \
stdout_logfile=/dev/stdout \
stdout_logfile_maxbytes=0 \
stderr_logfile=/dev/stderr \
stderr_logfile_maxbytes=0 \
' > /etc/supervisor/conf.d/supervisord.conf

# Créer un script d'entrypoint simplifié
RUN echo '#!/bin/sh \
set -e \
mkdir -p /var/www/storage/app/public \
mkdir -p /var/www/storage/framework/{sessions,views,cache} \
mkdir -p /var/www/storage/logs \
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
if [ ! -L /var/www/public/storage ]; then \
  su -s /bin/sh -c "php /var/www/artisan storage:link" www-data \
fi \
if [ -n "$DB_HOST" ]; then \
  counter=0 \
  until nc -z "$DB_HOST" "${DB_PORT:-3306}" > /dev/null 2>&1 || [ $counter -eq 6 ]; do \
    counter=$((counter+1)) \
    sleep 5 \
  done \
  if nc -z "$DB_HOST" "${DB_PORT:-3306}" > /dev/null 2>&1; then \
    su -s /bin/sh -c "php /var/www/artisan migrate --force" www-data || true \
  fi \
fi \
su -s /bin/sh -c "php /var/www/artisan config:cache" www-data \
su -s /bin/sh -c "php /var/www/artisan route:cache" www-data \
exec "$@" \
' > /entrypoint.sh \
&& chmod +x /entrypoint.sh

# Exposer le port
EXPOSE ${PORT}

# Définir le point d'entrée et la commande
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 