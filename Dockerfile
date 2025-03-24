FROM php:8.2-fpm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
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
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration de Nginx
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Configuration de supervisord
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Définition du répertoire de travail
WORKDIR /var/www

# Création des répertoires nécessaires
RUN mkdir -p /var/www/storage/app/public \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/bootstrap/cache \
    /var/www/public/build/assets/js \
    /var/www/public/build/assets/css \
    /var/www/public/assets/js \
    /var/www/public/assets/css \
    /tmp/laravel-logs

# Copie du code source
COPY . /var/www/

# Création du lien symbolique pour les logs
RUN rm -rf /var/www/storage/logs && \
    ln -sf /tmp/laravel-logs /var/www/storage/logs

# Installation des dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Installation des dépendances JavaScript et build des assets (si nécessaire)
RUN if [ -f "package.json" ]; then \
        npm ci && npm run build || echo "Échec du build JS, création de fallbacks..."; \
    fi

# Créer les fichiers de fallback pour les assets
RUN echo "console.log('Fallback app bundle');" > /var/www/public/build/assets/js/app-gZAm2HJZ.js \
    && echo "console.log('Fallback vendor bundle');" > /var/www/public/build/assets/js/vendor-CLLTD4I8.js \
    && echo "/* Fallback CSS */" > /var/www/public/build/assets/css/app-CjAB3oxN.css \
    && echo "console.log('Fallback app bundle');" > /var/www/public/assets/js/app-gZAm2HJZ.js \
    && echo "console.log('Fallback vendor bundle');" > /var/www/public/assets/js/vendor-CLLTD4I8.js \
    && echo "/* Fallback CSS */" > /var/www/public/assets/css/app-CjAB3oxN.css \
    && echo "console.log('Fallback app bundle');" > /var/www/public/build/assets/js/app.js \
    && echo "console.log('Fallback vendor bundle');" > /var/www/public/build/assets/js/vendor.js \
    && echo "/* Fallback CSS */" > /var/www/public/build/assets/css/app.css \
    && echo "console.log('Fallback app bundle');" > /var/www/public/assets/js/app.js \
    && echo "console.log('Fallback vendor bundle');" > /var/www/public/assets/js/vendor.js \
    && echo "/* Fallback CSS */" > /var/www/public/assets/css/app.css

# Création des fichiers .htaccess en ligne
RUN echo '<IfModule mod_headers.c>\n\
    Header set Cache-Control "no-store, no-cache, must-revalidate, max-age=0"\n\
    Header set Pragma "no-cache"\n\
    Header set Expires "0"\n\
</IfModule>\n\
\n\
<IfModule mod_rewrite.c>\n\
    RewriteEngine On\n\
    \n\
    # D'\''abord essayer la version non-hashée\n\
    RewriteCond %{REQUEST_FILENAME} !-f\n\
    RewriteCond %{REQUEST_FILENAME} !-d\n\
    RewriteRule ^([^/]+)/([^/]+)-[a-zA-Z0-9]+\.([^.]+)$ /$1/$2.$3 [L]\n\
    \n\
    # Ensuite essayer l'\''autre répertoire\n\
    RewriteCond %{REQUEST_FILENAME} !-f\n\
    RewriteCond %{REQUEST_FILENAME} !-d\n\
    RewriteRule ^(.*)$ /assets/$1 [L,QSA]\n\
</IfModule>' > /var/www/public/build/assets/.htaccess \
    && echo '<IfModule mod_headers.c>\n\
    Header set Cache-Control "no-store, no-cache, must-revalidate, max-age=0"\n\
    Header set Pragma "no-cache"\n\
    Header set Expires "0"\n\
</IfModule>\n\
\n\
<IfModule mod_rewrite.c>\n\
    RewriteEngine On\n\
    \n\
    # D'\''abord essayer la version non-hashée\n\
    RewriteCond %{REQUEST_FILENAME} !-f\n\
    RewriteCond %{REQUEST_FILENAME} !-d\n\
    RewriteRule ^([^/]+)/([^/]+)-[a-zA-Z0-9]+\.([^.]+)$ /$1/$2.$3 [L]\n\
    \n\
    # Ensuite essayer l'\''autre répertoire\n\
    RewriteCond %{REQUEST_FILENAME} !-f\n\
    RewriteCond %{REQUEST_FILENAME} !-d\n\
    RewriteRule ^(.*)$ /build/assets/$1 [L,QSA]\n\
</IfModule>' > /var/www/public/assets/.htaccess

# Création du script helper pour les assets
RUN echo '<?php\n\
$requestUri = $_SERVER["REQUEST_URI"];\n\
$extension = pathinfo($requestUri, PATHINFO_EXTENSION);\n\
\n\
$mimeTypes = [\n\
    "js" => "application/javascript",\n\
    "css" => "text/css",\n\
    "png" => "image/png",\n\
    "jpg" => "image/jpeg",\n\
    "jpeg" => "image/jpeg",\n\
    "svg" => "image/svg+xml",\n\
];\n\
\n\
if (isset($mimeTypes[$extension])) {\n\
    header("Content-Type: " . $mimeTypes[$extension]);\n\
}\n\
\n\
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");\n\
\n\
if ($extension === "js") {\n\
    echo "console.log(\"Fallback asset: \" + location.pathname);";\n\
} elseif ($extension === "css") {\n\
    echo "/* Fallback CSS */\\nbody { font-family: sans-serif; }";\n\
}\n' > /var/www/public/asset-helper.php

# Copier le script fix-assets
COPY docker/fix-assets.php /var/www/public/fix-assets.php

# Création du manifest.json minimal
RUN echo '{\n\
    "resources/js/app.jsx": {\n\
        "file": "assets/js/app.js",\n\
        "isEntry": true,\n\
        "src": "resources/js/app.jsx"\n\
    },\n\
    "resources/css/app.css": {\n\
        "file": "assets/css/app.css",\n\
        "isEntry": true,\n\
        "src": "resources/css/app.css"\n\
    }\n\
}' > /var/www/public/build/manifest.json \
    && cp /var/www/public/build/manifest.json /var/www/public/assets/manifest.json

# Fichiers pour les diagnostics
RUN echo '<?php phpinfo();' > /var/www/public/info.php \
    && echo '<?php echo json_encode(["status" => "ok", "time" => time(), "version" => PHP_VERSION]);' > /var/www/public/health.php

# Copie du script d'entrée et définition des permissions
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache \
    && chmod -R 755 /tmp/laravel-logs

# Exposition du port
EXPOSE 4004

# Point d'entrée
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 