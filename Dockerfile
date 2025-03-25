FROM php:8.2-fpm AS base

LABEL maintainer="Pivot Marketplace <contact@pivot.fr>"

ENV DEBIAN_FRONTEND=noninteractive
# Set default queue to sync for reliability during deployment
ENV QUEUE_CONNECTION=sync

# 1. Installation des dépendances système et extensions PHP en une seule étape
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl nginx supervisor \
    libfreetype6-dev libjpeg62-turbo-dev libicu-dev libzip-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mbstring exif pcntl bcmath zip intl \
    && docker-php-ext-enable opcache \
    && { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# 2. Installation de nodejs et Composer
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Copier les configurations et scripts
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY docker/setup-images.sh /usr/local/bin/setup-images.sh
COPY docker/check-components.sh /usr/local/bin/check-components.sh
COPY docker/optimize-build.sh /usr/local/bin/optimize-build.sh
RUN chmod +x /usr/local/bin/setup-images.sh \
    && chmod +x /usr/local/bin/check-components.sh \
    && chmod +x /usr/local/bin/optimize-build.sh

# 4. Préparation du répertoire de travail
WORKDIR /var/www
RUN mkdir -p /var/www/public/imagesAccueil \
    && mkdir -p /var/log/laravel \
    && chown -R www-data:www-data /var/www

# 5. Copier et installer les dépendances en une seule étape
COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-scripts --no-autoloader --quiet \
    && npm ci --quiet --no-audit

# 6. Copier le code source et finaliser l'installation
COPY --chown=www-data:www-data resources/js/Pages/Welcome.jsx resources/js/Pages/Welcome.jsx.original
COPY --chown=www-data:www-data . .
RUN composer dump-autoload --optimize --quiet

# 7. Préparation des répertoires et liens symboliques
RUN rm -rf storage/logs && ln -sf /var/log/laravel storage/logs \
    && mkdir -p storage/app/public/imagesAccueil \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && php artisan storage:link || true \
    && /usr/local/bin/setup-images.sh

# 8. Vérifier les composants et optimiser la construction
RUN /usr/local/bin/check-components.sh \
    && /usr/local/bin/optimize-build.sh \
    && grep -q "export function isAbsoluteUrl" /var/www/resources/js/Utils/ImageHelper.js || \
    echo "export function isAbsoluteUrl(url) { if (!url) return false; return url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//'); }" >> /var/www/resources/js/Utils/ImageHelper.js

# 9. Optimisation Laravel en une seule étape (éviter les bootstraps multiples)
RUN php artisan migrate --force || echo "Migrations failed - will be handled at runtime" \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# 10. Compilation des assets et nettoyage
RUN NODE_OPTIONS="--max-old-space-size=2048" npm run build || echo "Asset build failed, but continuing" \
    && rm -rf /tmp/npm-cache \
    && rm -rf node_modules

# 11. Créer un script d'initialisation pour gérer les migrations au démarrage
RUN echo '#!/bin/bash\n\
php artisan migrate --force\n\
exec "$@"\n' > /usr/local/bin/docker-entrypoint.sh \
&& chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 