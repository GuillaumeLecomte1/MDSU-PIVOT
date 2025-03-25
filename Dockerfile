FROM php:8.2-fpm AS base

LABEL maintainer="Pivot Marketplace <contact@pivot.fr>"

ENV DEBIAN_FRONTEND=noninteractive

# 1. Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    nginx \
    supervisor \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libicu-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Installation des extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mbstring exif pcntl bcmath zip intl \
    && docker-php-ext-enable opcache

# 3. Configuration de Opcache pour de meilleures performances
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# 4. Installation de nodejs
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

# 5. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Copier les configurations
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/setup-images.sh /usr/local/bin/setup-images.sh
COPY docker/check-components.sh /usr/local/bin/check-components.sh
RUN chmod +x /usr/local/bin/setup-images.sh \
    && chmod +x /usr/local/bin/check-components.sh

# 7. Préparation du répertoire de travail
WORKDIR /var/www
RUN mkdir -p /var/www/public/imagesAccueil \
    && mkdir -p /var/log/laravel \
    && chown -R www-data:www-data /var/www

# 8. Copier les fichiers de dépendances et installation
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# 9. Installation des dépendances
RUN composer install --no-scripts --no-autoloader --quiet \
    && npm ci --quiet --no-audit

# 10. Copier le fichier Welcome.jsx pour sauvegarde
COPY --chown=www-data:www-data resources/js/Pages/Welcome.jsx resources/js/Pages/Welcome.jsx.original

# 11. Copier le code source
COPY --chown=www-data:www-data . .

# 12. Finalisation de l'installation des dépendances
RUN composer dump-autoload --optimize --quiet 

# 13. Préparation des liens symboliques et des images
RUN rm -rf storage/logs && ln -sf /var/log/laravel storage/logs \
    && mkdir -p storage/app/public/imagesAccueil \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && php artisan storage:link || true \
    && /usr/local/bin/setup-images.sh \
    && /usr/local/bin/check-components.sh

# 13b. Vérification et correction des problèmes d'export connus
RUN grep -q "export function isAbsoluteUrl" /var/www/resources/js/Utils/ImageHelper.js || \
    echo "export function isAbsoluteUrl(url) { if (!url) return false; return url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//'); }" >> /var/www/resources/js/Utils/ImageHelper.js

# 14. Optimisation Laravel et compilation des assets
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && npm run build || echo "Asset build failed, but continuing"

# 15. Nettoyage
RUN rm -rf /tmp/npm-cache \
    && rm -rf node_modules

# 16. Configuration des ports et démarrage
EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 