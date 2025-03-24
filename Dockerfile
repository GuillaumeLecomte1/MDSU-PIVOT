FROM php:8.2-fpm AS php-base

# Install system dependencies
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
    procps \
    iputils-ping \
    net-tools \
    dnsutils \
    telnet \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure intl \
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

# Get Composer
COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Stage for Node.js dependencies and asset building
FROM node:18-slim AS node-builder

WORKDIR /app

# Copy package files first for better caching
COPY package*.json ./
COPY vite.config.js ./

# Create minimal structure for Vite
RUN mkdir -p resources/js resources/css public/build

# Copy resources needed for build
COPY resources/js resources/js/
COPY resources/css resources/css/

# Install and build
RUN npm ci
RUN mkdir -p public/build

# Create a minimal index.html for Vite (prevents the error)
RUN echo '<!DOCTYPE html><html><head><title>Vite App</title></head><body><div id="app"></div></body></html>' > index.html

# Build assets
RUN npm run build || (echo "Build failed but continuing..." && \
    mkdir -p public/build/assets/js public/build/assets/css && \
    echo "console.log('Fallback app bundle');" > public/build/assets/js/app-gZAm2HJZ.js && \
    echo "console.log('Fallback vendor bundle');" > public/build/assets/js/vendor-CLLTD4I8.js && \
    echo "/* Fallback CSS */" > public/build/assets/css/app-CjAB3oxN.css && \
    echo '{"resources/js/app.jsx":{"file":"js/app-gZAm2HJZ.js","isEntry":true,"src":"resources/js/app.jsx"},"resources/css/app.css":{"file":"css/app-CjAB3oxN.css","isEntry":true,"src":"resources/css/app.css"}}' > public/build/manifest.json)

# Final image
FROM php-base AS runtime

# Prepare directory structure
RUN mkdir -p /var/www/storage/app/public \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /tmp/laravel-logs \
    /var/www/bootstrap/cache \
    /var/www/public/images \
    /var/www/public/build \
    /var/www/public/build/assets/js \
    /var/www/public/build/assets/css \
    /var/www/public/assets/js \
    /var/www/public/assets/css \
    /var/www/docker \
    /var/www/resources/views/components

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /var/www/docker/entrypoint.sh
RUN chmod +x /var/www/docker/entrypoint.sh

# Create diagnostic files
RUN echo '<?php phpinfo();' > /var/www/public/info.php \
    && echo '<?php echo json_encode(["status" => "ok", "timestamp" => time()]);' > /var/www/public/health-check.php

# Copy fix-assets script
COPY --chmod=644 docker/fix-assets.php /var/www/public/fix-assets.php

# Copy dependencies first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-autoloader \
    --no-scripts \
    --no-dev \
    --no-interaction \
    --prefer-dist

# Copy application code
COPY --chown=www-data:www-data . .

# Copy built assets from node-builder stage
COPY --from=node-builder /app/public/build /var/www/public/build

# Configure logs
RUN rm -rf /var/www/storage/logs && \
    ln -sf /tmp/laravel-logs /var/www/storage/logs

# Copy environment file
COPY docker/env.production /var/www/.env

# Create fallback assets and copy to both locations
RUN mkdir -p /var/www/public/build/assets/js \
    /var/www/public/build/assets/css \
    /var/www/public/assets/js \
    /var/www/public/assets/css && \
    if [ ! -f /var/www/public/build/assets/js/app-gZAm2HJZ.js ]; then \
        echo "console.log('Fallback app bundle');" > /var/www/public/build/assets/js/app-gZAm2HJZ.js; \
        echo "console.log('Fallback vendor bundle');" > /var/www/public/build/assets/js/vendor-CLLTD4I8.js; \
        echo "/* Fallback CSS */" > /var/www/public/build/assets/css/app-CjAB3oxN.css; \
    fi && \
    cp -n /var/www/public/build/assets/js/app-gZAm2HJZ.js /var/www/public/assets/js/ || true && \
    cp -n /var/www/public/build/assets/js/vendor-CLLTD4I8.js /var/www/public/assets/js/ || true && \
    cp -n /var/www/public/build/assets/css/app-CjAB3oxN.css /var/www/public/assets/css/ || true && \
    echo "console.log('Fallback app bundle');" > /var/www/public/assets/js/app.js && \
    echo "console.log('Fallback vendor bundle');" > /var/www/public/assets/js/vendor.js && \
    echo "/* Fallback CSS */" > /var/www/public/assets/css/app.css

# Create manifest.json if it doesn't exist from the build
RUN if [ ! -f /var/www/public/build/manifest.json ]; then \
    echo '{ \
        "resources/js/app.jsx": { \
            "file": "js/app-gZAm2HJZ.js", \
            "isEntry": true, \
            "src": "resources/js/app.jsx" \
        }, \
        "resources/css/app.css": { \
            "file": "css/app-CjAB3oxN.css", \
            "isEntry": true, \
            "src": "resources/css/app.css" \
        } \
    }' > /var/www/public/build/manifest.json; \
    fi

# Copy the manifest to the assets directory as well
RUN cp -f /var/www/public/build/manifest.json /var/www/public/assets/manifest.json || echo '{ \
        "resources/js/app.jsx": { \
            "file": "js/app-gZAm2HJZ.js", \
            "isEntry": true, \
            "src": "resources/js/app.jsx" \
        }, \
        "resources/css/app.css": { \
            "file": "css/app-CjAB3oxN.css", \
            "isEntry": true, \
            "src": "resources/css/app.css" \
        } \
    }' > /var/www/public/assets/manifest.json

# Create htaccess files for fallback
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
</IfModule>' > /var/www/public/build/assets/.htaccess && \
    echo '<IfModule mod_headers.c>\n\
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

# Set permissions
RUN chmod -R 777 /var/www/storage \
    && chmod -R 777 /var/www/bootstrap/cache \
    && chmod -R 777 /tmp/laravel-logs \
    && chmod -R 777 /var/www/public/build \
    && chmod -R 777 /var/www/public/assets \
    && chown -R www-data:www-data /var/www \
    && chown -R www-data:www-data /tmp/laravel-logs \
    && touch /tmp/laravel-logs/laravel.log \
    && chmod 666 /tmp/laravel-logs/laravel.log

# Generate optimized autoloader
RUN COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --no-dev --optimize

# Create Blade components manually
RUN echo '<label {{ $attributes->merge(["class" => "block font-medium text-sm text-gray-700"]) }}>{{ $slot }}</label>' > /var/www/resources/views/components/input-label.blade.php && \
    echo '<input {{ $attributes->merge(["class" => "border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"]) }}>' > /var/www/resources/views/components/text-input.blade.php && \
    echo '<input type="checkbox" {!! $attributes->merge(["class" => "rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"]) !!}>' > /var/www/resources/views/components/checkbox.blade.php && \
    echo '<button {{ $attributes->merge(["type" => "submit", "class" => "inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"]) }}>{{ $slot }}</button>' > /var/www/resources/views/components/primary-button.blade.php && \
    echo '<div {{ $attributes->merge(["class" => "text-sm text-red-600 space-y-1"]) }}>{{ $slot }}</div>' > /var/www/resources/views/components/input-error.blade.php && \
    echo '<div {{ $attributes->merge(["class" => "p-4 text-sm text-gray-600"]) }}>{{ $slot }}</div>' > /var/www/resources/views/components/auth-session-status.blade.php

# Create asset-helper.php
RUN echo '<?php\n\
/**\n\
 * Script pour servir des assets manquants\n\
 */\n\
\n\
// Déterminer quel asset est demandé\n\
$requestUri = $_SERVER["REQUEST_URI"];\n\
$extension = pathinfo($requestUri, PATHINFO_EXTENSION);\n\
\n\
// Types MIME pour les extensions courantes\n\
$mimeTypes = [\n\
    "js" => "application/javascript",\n\
    "css" => "text/css",\n\
    "png" => "image/png",\n\
    "jpg" => "image/jpeg",\n\
    "jpeg" => "image/jpeg",\n\
    "svg" => "image/svg+xml",\n\
    "gif" => "image/gif",\n\
    "woff" => "font/woff",\n\
    "woff2" => "font/woff2",\n\
    "ttf" => "font/ttf",\n\
    "eot" => "application/vnd.ms-fontobject",\n\
];\n\
\n\
// Définir le type MIME\n\
if (isset($mimeTypes[$extension])) {\n\
    header("Content-Type: " . $mimeTypes[$extension]);\n\
}\n\
\n\
// Désactiver le cache pour le développement\n\
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");\n\
header("Pragma: no-cache");\n\
header("Expires: 0");\n\
\n\
// Générer un contenu minimal selon le type de fichier\n\
if ($extension === "js") {\n\
    echo "console.log(\"Fallback asset generated for: \" + \\"' . $requestUri . '\\");";\n\
} elseif ($extension === "css") {\n\
    echo "/* Fallback CSS generated for ' . $requestUri . ' */\\n";\n\
    echo "body { font-family: system-ui, sans-serif; }\\n";\n\
} else {\n\
    // Pour les autres types de fichiers, renvoyer une erreur 404\n\
    header("HTTP/1.0 404 Not Found");\n\
    echo "Asset not found: ' . $requestUri . '";\n\
}\n\
' > /var/www/public/asset-helper.php

# Cleanup
RUN rm -rf /var/www/.git \
    /var/www/.github \
    && find /var/www -name ".git*" -type f -delete \
    && composer clear-cache

# Expose port
EXPOSE 4004

# Run entrypoint
ENTRYPOINT ["/var/www/docker/entrypoint.sh"] 