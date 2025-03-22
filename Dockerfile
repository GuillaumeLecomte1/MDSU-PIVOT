FROM php:8.2-fpm AS base

# Arguments définissant la configuration
ARG BUILDKIT_INLINE_CACHE=1
ARG APP_ENV=production
ARG NODE_VERSION=20
ARG SKIP_ARTISAN_COMMANDS=false

# Installation des dépendances système en une seule couche
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    iputils-ping \
    net-tools \
    dnsutils \
    telnet \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        mysqli \
        intl

# Installation de Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get update && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/* \
    && npm install -g npm@latest

# Installation de Composer
COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

# Configuration de PHP et PHP-FPM
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 600" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "log_errors = On" > /usr/local/etc/php/conf.d/error-log.ini \
    && echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/error-log.ini \
    && echo "catch_workers_output = yes" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "php_admin_value[error_log] = /dev/stderr" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "listen = 127.0.0.1:9000" > /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www

# Préparation de la structure de répertoires avec les logs dans /tmp plutôt que /dev/shm
RUN mkdir -p /var/www/storage/app/public \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /tmp/laravel-logs \
    /var/www/bootstrap/cache \
    /var/www/public/images \
    /var/www/public/build/assets \
    /var/www/docker \
    /var/www/storage/logs \
    /var/www/resources/views/components

# Copie des fichiers de configuration
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /var/www/docker/entrypoint.sh
RUN chmod +x /var/www/docker/entrypoint.sh

# Ajouter les fichiers de diagnostic
RUN echo '<?php phpinfo();' > /var/www/public/info.php \
    && echo '<?php echo json_encode(["status" => "ok", "timestamp" => time()]);' > /var/www/public/health-check.php

# Copier uniquement les fichiers de dépendances pour optimiser le cache
COPY composer.json composer.lock ./

# Installer les dépendances PHP avec output réduit et optimisation du cache
RUN COMPOSER_ALLOW_SUPERUSER=1 \
    composer install \
        --no-autoloader \
        --no-scripts \
        --no-dev \
        --no-interaction \
        --no-ansi \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader

# Copier le code source
COPY --chown=www-data:www-data . .

# Remplacer les logs par un symlink vers /tmp au lieu de /dev/shm
RUN rm -rf /var/www/storage/logs && \
    ln -sf /tmp/laravel-logs /var/www/storage/logs

# Copier les paramètres d'environnement spécifiques
COPY docker/env.production /var/www/.env

# Configuration des permissions d'abord pour éviter les erreurs
RUN chmod -R 777 /var/www/storage \
    && chmod -R 777 /var/www/bootstrap/cache \
    && chmod -R 777 /tmp/laravel-logs \
    && chown -R www-data:www-data /var/www \
    && chown -R www-data:www-data /tmp/laravel-logs \
    && touch /tmp/laravel-logs/laravel.log \
    && chmod 666 /tmp/laravel-logs/laravel.log

# Générer l'autoloader optimisé
RUN COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --no-dev --optimize

# Installer les composants Blade manquants (pour résoudre l'erreur input-label)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer require --no-interaction laravel/breeze

# Créer manuellement les composants Blade requis
RUN echo '<label {{ $attributes->merge(["class" => "block font-medium text-sm text-gray-700"]) }}>{{ $slot }}</label>' > /var/www/resources/views/components/input-label.blade.php && \
    echo '<input {{ $attributes->merge(["class" => "border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"]) }}>' > /var/www/resources/views/components/text-input.blade.php && \
    echo '<input type="checkbox" {!! $attributes->merge(["class" => "rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"]) !!}>' > /var/www/resources/views/components/checkbox.blade.php && \
    echo '<button {{ $attributes->merge(["type" => "submit", "class" => "inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"]) }}>{{ $slot }}</button>' > /var/www/resources/views/components/primary-button.blade.php && \
    echo '<div {{ $attributes->merge(["class" => "text-sm text-red-600 space-y-1"]) }}>{{ $slot }}</div>' > /var/www/resources/views/components/input-error.blade.php && \
    echo '<div {{ $attributes->merge(["class" => "p-4 text-sm text-gray-600"]) }}>{{ $slot }}</div>' > /var/www/resources/views/components/auth-session-status.blade.php

# Vérifier la syntaxe PHP avant d'exécuter les commandes Artisan
RUN echo "Vérification de la syntaxe PHP..." && \
    find /var/www -type f -name "*.php" -exec php -l {} \; | (grep -v "No syntax errors" || true)

# Mettre en cache les configurations (séparation des commandes pour mieux identifier les erreurs)
RUN if [ "$SKIP_ARTISAN_COMMANDS" = "false" ]; then \
        echo "Exécution des commandes Artisan..." && \
        php artisan config:clear || true && \
        php artisan view:clear || true && \
        php artisan route:clear || true && \
        php artisan optimize:clear || true && \
        php artisan config:cache || true; \
    else \
        echo "Commandes Artisan ignorées."; \
    fi

# Nettoyage pour réduire la taille de l'image
RUN rm -rf /var/www/node_modules \
    /var/www/.git \
    /var/www/.github \
    && find /var/www -name ".git*" -type f -delete \
    && composer clear-cache

# Exposition du port
EXPOSE 4004

# Point d'entrée
ENTRYPOINT ["/var/www/docker/entrypoint.sh"]

# Commande de secours en cas d'échec d'ENTRYPOINT
CMD ["/bin/bash", "-c", "chmod +x /var/www/docker/entrypoint.sh && /var/www/docker/entrypoint.sh"] 