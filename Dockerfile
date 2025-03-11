FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get Composer
COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Create necessary directories
RUN mkdir -p /var/www/storage/app/public \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/public/images

# Configurer PHP pour une meilleure performance
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Configurer Nginx
RUN rm -f /etc/nginx/sites-enabled/default && \
    rm -f /etc/nginx/sites-available/default
COPY docker/nginx.conf /etc/nginx/sites-available/laravel.conf
RUN ln -s /etc/nginx/sites-available/laravel.conf /etc/nginx/sites-enabled/ && \
    mkdir -p /var/log/nginx && \
    touch /var/log/nginx/error.log && \
    touch /var/log/nginx/access.log && \
    chown -R www-data:www-data /var/log/nginx

# Configurer Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copier le script d'entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Copier le code source
COPY . /var/www/

# Installer les dépendances PHP
RUN cd /var/www && \
    COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-dev

# Installer les dépendances Node.js et construire les assets
ENV NODE_OPTIONS=--max_old_space_size=4096
RUN cd /var/www && \
    npm ci --production=false --no-audit --no-fund && \
    npm run build

# Définir les permissions
RUN chown -R www-data:www-data /var/www && \
    find /var/www/storage -type d -exec chmod 775 {} \; && \
    find /var/www/storage -type f -exec chmod 664 {} \; && \
    find /var/www/public -type d -exec chmod 755 {} \; && \
    find /var/www/public -type f -exec chmod 644 {} \;

# Vérifier que le fichier index.php existe
RUN if [ ! -f /var/www/public/index.php ]; then \
        echo "ERREUR: Le fichier index.php n'existe pas dans le répertoire public!" && \
        exit 1; \
    fi

# Exposer le port
EXPOSE 4004

# Utiliser le script d'entrypoint
ENTRYPOINT ["/entrypoint.sh"]

# Démarrer les services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 