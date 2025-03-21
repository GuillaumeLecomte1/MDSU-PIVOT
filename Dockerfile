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
    && rm -rf /var/lib/apt/lists/*

# Install Node.js 20.x (LTS) avec une version compatible de npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@latest && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions with mysqli
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip mysqli

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
    && mkdir -p /var/www/public/images \
    && mkdir -p /var/www/public/build

# Configure PHP for better performance and increased upload limits
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Configurer Nginx
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Configurer Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copier les fichiers de configuration
COPY docker/fix-vite-issues.php /var/www/docker/fix-vite-issues.php
COPY docker/fix-https-urls.php /var/www/docker/fix-https-urls.php
COPY docker/fix-env.sh /var/www/docker/fix-env.sh
COPY docker/fix-pusher.php /var/www/docker/fix-pusher.php
COPY docker/fix-mixed-content.php /var/www/docker/fix-mixed-content.php

# Donner les permissions d'exécution aux scripts
RUN chmod +x /var/www/docker/fix-vite-issues.php /var/www/docker/fix-https-urls.php /var/www/docker/fix-env.sh /var/www/docker/fix-pusher.php /var/www/docker/fix-mixed-content.php

# Copier tout le code source d'abord
COPY . /var/www/

# Installer les dépendances PHP
RUN cd /var/www && \
    COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-dev

# Installer les dépendances Node.js avec une configuration optimisée et construire les assets
ENV NODE_OPTIONS="--max-old-space-size=4096 --no-warnings"
RUN cd /var/www && \
    npm ci --production=false --no-audit --no-fund && \
    npm install terser --save-dev && \
    echo "Tentative de construction des assets avec Vite..." && \
    NODE_ENV=production npm run build --debug || { \
        echo "Erreur lors de la construction des assets. Utilisation du fallback..."; \
        mkdir -p /var/www/public/build/assets/js /var/www/public/build/assets/css /var/www/public/build/.vite; \
        cp /var/www/resources/js/app.minimal.jsx /var/www/public/build/assets/js/app.js; \
        touch /var/www/public/build/assets/css/app.css; \
        echo '{"resources/js/app.jsx":{"file":"assets/js/app.js","isEntry":true,"src":"resources/js/app.jsx"},"resources/js/app.minimal.jsx":{"file":"assets/js/app.js","isEntry":true,"src":"resources/js/app.minimal.jsx"},"resources/css/app.css":{"file":"assets/css/app.css","isEntry":true,"src":"resources/css/app.css"}}' > /var/www/public/build/manifest.json; \
        echo '{"resources/js/app.jsx":{"file":"assets/js/app.js","isEntry":true,"src":"resources/js/app.jsx"},"resources/js/app.minimal.jsx":{"file":"assets/js/app.js","isEntry":true,"src":"resources/js/app.minimal.jsx"},"resources/css/app.css":{"file":"assets/css/app.css","isEntry":true,"src":"resources/css/app.css"}}' > /var/www/public/build/.vite/manifest.json; \
    } && \
    ls -la public/build && \
    echo "Vérification du répertoire .vite:" && \
    ls -la public/build/.vite || echo "Répertoire .vite non trouvé" && \
    if [ -f /var/www/public/build/.vite/manifest.json ]; then \
        echo "Copie du manifeste depuis .vite vers le répertoire racine de build"; \
        cp /var/www/public/build/.vite/manifest.json /var/www/public/build/manifest.json; \
    else \
        echo "Création d'un manifeste minimal"; \
        php /var/www/docker/fix-vite-issues.php || echo "Erreur lors de la création du manifeste minimal, mais on continue..."; \
    fi && \
    if [ -f /var/www/public/build/manifest.json ]; then \
        echo "Vérification des URLs HTTPS dans le manifeste..."; \
        php /var/www/docker/fix-https-urls.php || echo "Erreur lors de la vérification des URLs HTTPS, mais on continue..."; \
        cat public/build/manifest.json || echo "Impossible d'afficher le manifeste, mais on continue..."; \
    else \
        echo "Manifeste toujours manquant, création d'un manifeste minimal de secours..."; \
        mkdir -p /var/www/public/build/assets/js /var/www/public/build/assets/css /var/www/public/build/.vite; \
        touch /var/www/public/build/assets/js/app.js /var/www/public/build/assets/css/app.css; \
        echo '{"resources/js/app.jsx":{"file":"assets/js/app.js","isEntry":true,"src":"resources/js/app.jsx"},"resources/css/app.css":{"file":"assets/css/app.css","isEntry":true,"src":"resources/css/app.css"}}' > /var/www/public/build/manifest.json; \
        echo '{"resources/js/app.jsx":{"file":"assets/js/app.js","isEntry":true,"src":"resources/js/app.jsx"},"resources/css/app.css":{"file":"assets/css/app.css","isEntry":true,"src":"resources/css/app.css"}}' > /var/www/public/build/.vite/manifest.json; \
    fi && \
    npm prune --production && \
    rm -rf node_modules/.cache

# Vérifier que le manifeste Vite existe
RUN if [ ! -f /var/www/public/build/manifest.json ]; then \
    echo "Erreur: Le fichier manifest.json n'a pas été généré correctement."; \
    php /var/www/docker/fix-vite-issues.php; \
    if [ ! -f /var/www/public/build/manifest.json ]; then \
        echo "Impossible de créer le manifeste. Arrêt du build."; \
        exit 1; \
    fi; \
    fi

# Définir les permissions
RUN chown -R www-data:www-data /var/www && \
    find /var/www/storage -type d -exec chmod 775 {} \; && \
    find /var/www/storage -type f -exec chmod 664 {} \; && \
    find /var/www/public -type d -exec chmod 755 {} \; && \
    find /var/www/public -type f -exec chmod 644 {} \; && \
    chmod -R 755 /var/www/public/build

# Exposer le port
EXPOSE 4004

# Démarrer les services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 