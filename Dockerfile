FROM php:8.2-fpm-alpine AS php-base

# Installer les dépendances système et extensions PHP en une seule couche
RUN apk add --no-cache \
    # Dépendances système de base
    git \
    curl \
    zip \
    unzip \
    nginx \
    supervisor \
    # Dépendances pour les extensions PHP
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    # Pour le healthcheck
    postgresql-client \
    mysql-client \
    # Installez les extensions PHP
    && docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd zip \
    && docker-php-ext-configure intl && docker-php-ext-install intl \
    # Optimisez PHP pour la production
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    # Créez le répertoire d'exécution pour nginx
    && mkdir -p /run/nginx

# Optimiser la configuration PHP pour Laravel en production
RUN echo "upload_max_filesize = 64M" > $PHP_INI_DIR/conf.d/upload.ini \
    && echo "post_max_size = 64M" >> $PHP_INI_DIR/conf.d/upload.ini \
    && echo "memory_limit = 512M" >> $PHP_INI_DIR/conf.d/upload.ini \
    && echo "max_execution_time = 120" >> $PHP_INI_DIR/conf.d/upload.ini \
    && echo "opcache.enable=1" > $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.enable_cli=1" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.jit_buffer_size=128M" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.jit=1255" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> $PHP_INI_DIR/conf.d/opcache.ini

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www

# Étape de build Node.js
FROM node:18-alpine AS node-build
WORKDIR /var/www

# Copier uniquement les fichiers nécessaires pour l'installation des packages npm
COPY package*.json ./
RUN npm ci --quiet --no-audit --production=false

# Copier le reste des fichiers source
COPY . .

# Construire les assets pour la production avec Vite
ENV NODE_ENV=production
RUN npm run build \
    && mkdir -p public/build \
    && if [ -f "public/build/.vite/manifest.json" ]; then \
          cp public/build/.vite/manifest.json public/build/manifest.json; \
       else \
          echo "Erreur: manifest.json non trouvé"; \
          exit 1; \
       fi

# Étape de build PHP
FROM php-base AS php-build
WORKDIR /var/www

# Copier le code de l'application
COPY . /var/www/

# Copier les assets compilés depuis l'étape node-build
COPY --from=node-build /var/www/public/build /var/www/public/build

# Installer les dépendances PHP et optimiser
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts \
    && composer dump-autoload --optimize \
    # Créer la structure de répertoires nécessaire
    && mkdir -p storage/app/public \
    && mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    # Définir les permissions
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache

# Étape finale
FROM php-base
ARG PORT=4004
ENV PORT=${PORT} \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

# Copier le code de l'application depuis l'étape de build PHP
COPY --from=php-build --chown=www-data:www-data /var/www /var/www

# Créer les répertoires et fichiers de configuration
RUN mkdir -p /etc/supervisor/conf.d \
    && mkdir -p /var/www/docker/scripts \
    && mkdir -p /var/log/nginx \
    && mkdir -p /var/log/php

# Configurer nginx
RUN echo 'server { \
    listen '${PORT}'; \
    root /var/www/public; \
    index index.php; \
    charset utf-8; \
    client_max_body_size 64M; \
    # Gzip \
    gzip on; \
    gzip_comp_level 5; \
    gzip_min_length 256; \
    gzip_proxied any; \
    gzip_types application/javascript application/json text/css text/plain text/xml; \
    # Gérer les erreurs \
    error_page 404 /index.php; \
    # Favicon \
    location = /favicon.ico { access_log off; log_not_found off; } \
    location = /robots.txt { access_log off; log_not_found off; } \
    # Redirection principale \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    # Bloquer l'\''accès aux fichiers cachés \
    location ~ /\.(?!well-known).* { \
        deny all; \
    } \
    # Servir les fichiers statiques \
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|webp|woff|woff2|ttf|eot)$ { \
        expires 30d; \
        add_header Cache-Control "public, no-transform"; \
    } \
    # PHP via FastCGI \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        include fastcgi_params; \
        fastcgi_read_timeout 120; \
    } \
}' > /etc/nginx/http.d/default.conf

# Configurer PHP-FPM
RUN echo '[global] \
error_log = /proc/self/fd/2 \
daemonize = no \
\
[www] \
user = www-data \
group = www-data \
listen = 9000 \
pm = dynamic \
pm.max_children = 30 \
pm.start_servers = 5 \
pm.min_spare_servers = 2 \
pm.max_spare_servers = 10 \
pm.max_requests = 1000 \
access.log = /proc/self/fd/2 \
clear_env = no \
catch_workers_output = yes \
decorate_workers_output = no \
php_admin_value[memory_limit] = 512M \
php_admin_value[upload_max_filesize] = 64M \
php_admin_value[post_max_size] = 64M \
php_admin_value[display_errors] = Off \
php_admin_flag[log_errors] = on \
' > /usr/local/etc/php-fpm.d/www.conf

# Configurer supervisord
RUN echo '[supervisord] \
nodaemon=true \
user=root \
logfile=/dev/stdout \
logfile_maxbytes=0 \
\
[program:php-fpm] \
command=php-fpm -F \
stdout_logfile=/dev/stdout \
stdout_logfile_maxbytes=0 \
stderr_logfile=/dev/stderr \
stderr_logfile_maxbytes=0 \
autostart=true \
autorestart=true \
priority=5 \
\
[program:nginx] \
command=nginx -g "daemon off;" \
stdout_logfile=/dev/stdout \
stdout_logfile_maxbytes=0 \
stderr_logfile=/dev/stderr \
stderr_logfile_maxbytes=0 \
autostart=true \
autorestart=true \
priority=10 \
' > /etc/supervisor/conf.d/supervisord.conf

# Créer le script de healthcheck
RUN echo '#!/bin/sh \
\
# Vérifier si nginx et php-fpm sont en cours d'\''exécution \
if ! pgrep -x nginx > /dev/null; then \
  echo "Nginx n'\''est pas en cours d'\''exécution" >&2 \
  exit 1 \
fi \
\
if ! pgrep -x php-fpm > /dev/null; then \
  echo "PHP-FPM n'\''est pas en cours d'\''exécution" >&2 \
  exit 1 \
fi \
\
# Vérifier si l'\''application est accessible \
if ! curl -s -I -f http://localhost:'${PORT}' > /dev/null; then \
  echo "L'\''application n'\''est pas accessible" >&2 \
  exit 1 \
fi \
\
exit 0 \
' > /var/www/docker/scripts/healthcheck.sh \
&& chmod +x /var/www/docker/scripts/healthcheck.sh

# Créer le script d'entrypoint
RUN echo '#!/bin/sh \
set -e \
\
echo "🔧 Initialisation du conteneur..." \
\
# Créer les répertoires de stockage s'\''ils n'\''existent pas \
mkdir -p /var/www/storage/app/public \
mkdir -p /var/www/storage/framework/sessions \
mkdir -p /var/www/storage/framework/views \
mkdir -p /var/www/storage/framework/cache \
mkdir -p /var/www/storage/logs \
mkdir -p /var/www/bootstrap/cache \
\
# Définir les permissions correctes \
chown -R www-data:www-data /var/www/storage \
chown -R www-data:www-data /var/www/bootstrap/cache \
\
# Créer le lien symbolique pour le stockage \
if [ ! -L /var/www/public/storage ]; then \
  echo "📁 Création du lien symbolique pour storage..." \
  su -s /bin/sh -c "php /var/www/artisan storage:link" www-data \
fi \
\
# Attendre que la base de données soit disponible si DB_HOST est défini \
if [ -n "$DB_HOST" ]; then \
  echo "🔄 Attente de la disponibilité de la base de données à $DB_HOST..." \
  max_tries=30 \
  counter=0 \
  until nc -z "$DB_HOST" "${DB_PORT:-3306}" > /dev/null 2>&1; do \
    counter=$((counter+1)) \
    if [ $counter -ge $max_tries ]; then \
      echo "❌ Impossible de se connecter à la base de données après $max_tries tentatives." \
      break \
    fi \
    echo "⏳ Tentative $counter/$max_tries - Attente de la base de données..." \
    sleep 2 \
  done \
\
  # Exécuter les migrations si la base de données est disponible \
  if nc -z "$DB_HOST" "${DB_PORT:-3306}" > /dev/null 2>&1; then \
    echo "✅ Base de données disponible, exécution des migrations..." \
    su -s /bin/sh -c "php /var/www/artisan migrate --force" www-data \
  fi \
fi \
\
# Optimisations Laravel pour la production \
echo "⚙️ Optimisation de Laravel pour la production..." \
su -s /bin/sh -c "php /var/www/artisan config:cache" www-data \
su -s /bin/sh -c "php /var/www/artisan route:cache" www-data \
su -s /bin/sh -c "php /var/www/artisan view:cache" www-data \
\
echo "✅ Initialisation terminée, démarrage des services..." \
\
# Démarrer les services \
exec "$@" \
' > /entrypoint.sh \
&& chmod +x /entrypoint.sh

# Configurer le healthcheck Docker
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
  CMD /var/www/docker/scripts/healthcheck.sh

# Exposer le port
EXPOSE ${PORT}

# Définir le point d'entrée et la commande
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 