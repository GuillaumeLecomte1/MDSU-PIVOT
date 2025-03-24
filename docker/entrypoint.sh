#!/bin/bash
set -e

# Fonctions d'aide pour le formattage
function log_info() {
    echo -e "\033[0;34m[INFO]\033[0m $1"
}

function log_success() {
    echo -e "\033[0;32m[SUCCESS]\033[0m $1"
}

function log_warning() {
    echo -e "\033[0;33m[WARNING]\033[0m $1"
}

function log_error() {
    echo -e "\033[0;31m[ERROR]\033[0m $1"
}

# Gestion des erreurs
function handle_error() {
    log_error "Une erreur est survenue dans le script à la ligne $1"
}

trap 'handle_error $LINENO' ERR

# Début du script
log_info "====== DÉMARRAGE DU CONTENEUR PIVOT ======"
log_info "Date: $(date)"
log_info "Environnement: $APP_ENV"

# S'assurer que les répertoires de logs existent
mkdir -p /tmp/laravel-logs

# Configuration des permissions
log_info "Configuration des permissions..."
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache
chmod -R 777 /tmp/laravel-logs
chown -R www-data:www-data /var/www
chown -R www-data:www-data /tmp/laravel-logs
touch /tmp/laravel-logs/laravel.log
chmod 666 /tmp/laravel-logs/laravel.log

# S'assurer que le symlink des logs est correct
if [ ! -L /var/www/storage/logs ] || [ "$(readlink /var/www/storage/logs)" != "/tmp/laravel-logs" ]; then
    log_info "Recréation du symlink pour les logs..."
    rm -rf /var/www/storage/logs
    ln -sf /tmp/laravel-logs /var/www/storage/logs
fi

# Forcer la valeur de DB_HOST dans le fichier .env
log_info "====== MISE À JOUR DES PARAMETRES DB ======"
sed -i "s/DB_HOST=.*/DB_HOST=personnel-phpmyadmin-hj0arz-db-1/g" /var/www/.env
log_success "DB_HOST mis à jour vers personnel-phpmyadmin-hj0arz-db-1"

# Configuration du cache basée sur les fichiers au lieu de la base de données
sed -i "s/CACHE_STORE=database/CACHE_STORE=file/g" /var/www/.env
sed -i "s/QUEUE_CONNECTION=database/QUEUE_CONNECTION=sync/g" /var/www/.env

# Informations de base de données
log_info "====== INFORMATIONS DE BASE DE DONNÉES ======"
DB_HOST=$(grep DB_HOST /var/www/.env | cut -d= -f2)
DB_PORT=$(grep DB_PORT /var/www/.env | cut -d= -f2)
DB_NAME=$(grep DB_DATABASE /var/www/.env | cut -d= -f2)
DB_USER=$(grep DB_USERNAME /var/www/.env | cut -d= -f2)
DB_PASS=$(grep DB_PASSWORD /var/www/.env | cut -d= -f2)

log_info "Database Host: $DB_HOST"
log_info "Database Port: $DB_PORT"
log_info "Database Name: $DB_NAME"
log_info "Database User: $DB_USER"

# Attendre que MySQL soit prêt
log_info "Waiting for MySQL connection..."
max_attempts=30
counter=0
connected=false

# Afficher les informations de débogage
log_info "Attempting to connect with: mysql:host=$DB_HOST;dbname=$DB_NAME user=$DB_USER"

until php -r "
try {
    \$db = new PDO(
        'mysql:host=$DB_HOST;dbname=$DB_NAME',
        '$DB_USER',
        '$DB_PASS'
    );
    \$db->setAttribute(PDO::ATTR_TIMEOUT, 5);
    \$db->query('SELECT 1');
    echo 'Connected to MySQL';
    exit(0);
} catch (PDOException \$e) {
    echo \$e->getMessage();
    exit(1);
}" 2>/dev/null
do
  if [ $counter -eq $max_attempts ]; then
    log_warning "Failed to connect to MySQL after $max_attempts attempts. Continuing anyway..."
    break
  fi
  log_warning "MySQL connection attempt $((counter+1))/$max_attempts failed. Retrying in 2 seconds..."
  counter=$((counter+1))
  sleep 2
done

if [ $counter -lt $max_attempts ]; then
    log_success "Successfully connected to MySQL database"
    connected=true
else
    log_warning "Could not connect to MySQL, but continuing with startup"
    # Mettre APP_DEBUG à true pour voir les erreurs
    sed -i "s/APP_DEBUG=false/APP_DEBUG=true/g" /var/www/.env
fi

# Créer les composants Blade s'ils n'existent pas
log_info "====== VÉRIFICATION DES COMPOSANTS BLADE ======"
COMPONENTS_DIR="/var/www/resources/views/components"

if [ ! -f "$COMPONENTS_DIR/input-label.blade.php" ]; then
    log_info "Création des composants Blade manquants..."
    echo '<label {{ $attributes->merge(["class" => "block font-medium text-sm text-gray-700"]) }}>{{ $slot }}</label>' > $COMPONENTS_DIR/input-label.blade.php
    echo '<input {{ $attributes->merge(["class" => "border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"]) }}>' > $COMPONENTS_DIR/text-input.blade.php
    echo '<input type="checkbox" {!! $attributes->merge(["class" => "rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"]) !!}>' > $COMPONENTS_DIR/checkbox.blade.php
    echo '<button {{ $attributes->merge(["type" => "submit", "class" => "inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"]) }}>{{ $slot }}</button>' > $COMPONENTS_DIR/primary-button.blade.php
    echo '<div {{ $attributes->merge(["class" => "text-sm text-red-600 space-y-1"]) }}>{{ $slot }}</div>' > $COMPONENTS_DIR/input-error.blade.php
    echo '<div {{ $attributes->merge(["class" => "p-4 text-sm text-gray-600"]) }}>{{ $slot }}</div>' > $COMPONENTS_DIR/auth-session-status.blade.php
    chown -R www-data:www-data $COMPONENTS_DIR
    chmod -R 644 $COMPONENTS_DIR/*.blade.php
fi

# Nettoyage du cache
log_info "====== NETTOYAGE DU CACHE ======"
cd /var/www && php artisan optimize:clear

# Mise à jour des paramètres d'URL et d'assets
log_info "====== MISE À JOUR DES PARAMÈTRES D'URL ======"
sed -i "s|APP_URL=.*|APP_URL=http://pivot.guillaume-lcte.fr|g" /var/www/.env
sed -i "s|ASSET_URL=.*|ASSET_URL=http://pivot.guillaume-lcte.fr|g" /var/www/.env
log_success "URLs mises à jour en HTTP pour éviter les redirections HTTPS"

# Create health check file
log_info "Creating health check file"
echo "OK" > /var/www/public/health
chmod 644 /var/www/public/health
log_success "Health check file created"

# Create diagnostic file
log_info "====== CRÉATION DU FICHIER DE DIAGNOSTIC ======"
cat > /var/www/public/server-info.php << EOL
<?php
\$server = [
    'hostname' => gethostname(),
    'ip' => \$_SERVER['SERVER_ADDR'] ?? 'unknown',
    'date' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'software' => \$_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    'memory_usage' => memory_get_usage(true),
    'memory_limit' => ini_get('memory_limit'),
    'disk_free' => disk_free_space('/'),
    'disk_total' => disk_total_space('/'),
    'disk_usage' => round((disk_total_space('/') - disk_free_space('/')) / disk_total_space('/') * 100, 2) . '%',
    'server_load' => sys_getloadavg(),
    'uptime' => exec('uptime -p')
];

header('Content-Type: application/json');
echo json_encode(\$server, JSON_PRETTY_PRINT);
EOL
chmod 644 /var/www/public/server-info.php

# Vérification de la structure des répertoires
log_info "Vérification des répertoires..."
mkdir -p /var/www/storage/app/public \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/bootstrap/cache \
    /var/www/public/build/assets/js \
    /var/www/public/build/assets/css \
    /var/www/public/assets/js \
    /var/www/public/assets/css

# Génération des fichiers d'assets minimaux en cas d'absence
if [ ! -f /var/www/public/build/assets/js/app-gZAm2HJZ.js ]; then
    log_info "Génération de fichiers assets minimaux..."
    echo "console.log('Fallback app JS bundle generated in Docker');" > /var/www/public/build/assets/js/app-gZAm2HJZ.js
    echo "console.log('Fallback vendor JS bundle');" > /var/www/public/build/assets/js/vendor-CLLTD4I8.js
    echo "/* Fallback CSS file */" > /var/www/public/build/assets/css/app-CjAB3oxN.css
    
    # Versions sans hash
    cp /var/www/public/build/assets/js/app-gZAm2HJZ.js /var/www/public/build/assets/js/app.js
    cp /var/www/public/build/assets/js/vendor-CLLTD4I8.js /var/www/public/build/assets/js/vendor.js
    cp /var/www/public/build/assets/css/app-CjAB3oxN.css /var/www/public/build/assets/css/app.css
    
    # Copier également dans le répertoire assets
    mkdir -p /var/www/public/assets/js /var/www/public/assets/css
    cp /var/www/public/build/assets/js/app-gZAm2HJZ.js /var/www/public/assets/js/
    cp /var/www/public/build/assets/js/vendor-CLLTD4I8.js /var/www/public/assets/js/
    cp /var/www/public/build/assets/css/app-CjAB3oxN.css /var/www/public/assets/css/
    cp /var/www/public/build/assets/js/app.js /var/www/public/assets/js/
    cp /var/www/public/build/assets/js/vendor.js /var/www/public/assets/js/
    cp /var/www/public/build/assets/css/app.css /var/www/public/assets/css/
fi

# Vérification/création du manifest.json
if [ ! -f /var/www/public/build/manifest.json ]; then
    log_info "Création d'un manifest.json minimal..."
    cat > /var/www/public/build/manifest.json << EOL
{
    "resources/js/app.jsx": {
        "file": "js/app-gZAm2HJZ.js",
        "isEntry": true,
        "src": "resources/js/app.jsx"
    },
    "resources/css/app.css": {
        "file": "css/app-CjAB3oxN.css",
        "isEntry": true,
        "src": "resources/css/app.css"
    }
}
EOL
    cp /var/www/public/build/manifest.json /var/www/public/assets/manifest.json
fi

# Création des .htaccess pour la gestion des assets
log_info "Création des fichiers .htaccess pour les assets..."
cat > /var/www/public/build/assets/.htaccess << EOL
<IfModule mod_headers.c>
    Header set Cache-Control "max-age=31536000, public"
</IfModule>

<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to assets directory if file not found
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /assets/\$1 [L,QSA]
</IfModule>
EOL

cat > /var/www/public/assets/.htaccess << EOL
<IfModule mod_headers.c>
    Header set Cache-Control "max-age=31536000, public"
</IfModule>

<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to build/assets directory if file not found
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /build/assets/\$1 [L,QSA]
</IfModule>
EOL

# Optimisations Laravel en production
log_info "Configuration de Laravel pour la production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Création des liens symboliques pour storage
log_info "Création des liens symboliques..."
php artisan storage:link

# Définir les permissions
log_info "Définition des permissions..."
chmod -R 755 /var/www/public
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
chown -R www-data:www-data /var/www

log_success "====== DÉMARRAGE DE SUPERVISORD ======"
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf