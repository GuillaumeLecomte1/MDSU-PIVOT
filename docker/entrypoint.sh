#!/bin/bash
set -e

# Fonctions d'aide pour le formattage
function log_info() {
    echo -e "\e[34m[INFO]\e[0m $1"
}

function log_success() {
    echo -e "\e[32m[SUCCESS]\e[0m $1"
}

function log_warning() {
    echo -e "\e[33m[WARNING]\e[0m $1"
}

function log_error() {
    echo -e "\e[31m[ERROR]\e[0m $1"
}

# Gestion des erreurs
function handle_error() {
    log_error "Une erreur est survenue dans le script à la ligne $1"
}

trap 'handle_error $LINENO' ERR

# Début du script
log_info "====== DÉMARRAGE DU CONTENEUR ======"

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

# Diagnostics réseau
log_info "====== DIAGNOSTIC RÉSEAU ======"

# Test DNS pour MySQL
if command -v dig &> /dev/null; then
    log_info "Performing DNS lookup for MySQL:"
    dig mysql +short || log_warning "DNS lookup failed"
fi

# Test getent pour MySQL
if command -v getent &> /dev/null; then
    log_info "Looking up hosts for MySQL:"
    getent hosts mysql || log_warning "Host lookup failed"
fi

# Ajouter l'hôte MySQL à /etc/hosts si nécessaire
if ! grep -q "mysql" /etc/hosts; then
    log_info "Adding MySQL to /etc/hosts..."
    echo "# Ensuring MySQL connectivity" >> /etc/hosts
    
    # Tenter de résoudre l'IP depuis le réseau Docker
    MYSQL_IP=$(getent hosts mysql | awk '{ print $1 }') || MYSQL_IP="10.0.1.50"
    echo "${MYSQL_IP} mysql" >> /etc/hosts
    log_success "Added ${MYSQL_IP} as mysql to /etc/hosts"
fi

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

# S'assurer que MySQL est accessible
if command -v telnet &> /dev/null; then
    log_info "Testing MySQL connectivity with telnet..."
    timeout 5 telnet $DB_HOST $DB_PORT || log_warning "Could not connect to MySQL with telnet, continuing anyway..."
fi

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
        'mysql:host=\"$DB_HOST\";dbname=\"$DB_NAME\"', 
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

# Création d'un fichier de diagnostic
log_info "====== CRÉATION DU FICHIER DE DIAGNOSTIC ======"
cat > /var/www/public/server-info.php << 'EOL'
<?php
header('Content-Type: application/json');

$serverInfo = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    'environment' => [
        'APP_ENV' => getenv('APP_ENV'),
        'APP_DEBUG' => getenv('APP_DEBUG'),
        'APP_URL' => getenv('APP_URL')
    ],
    'database' => [
        'DB_CONNECTION' => getenv('DB_CONNECTION'),
        'DB_HOST' => getenv('DB_HOST'),
        'DB_PORT' => getenv('DB_PORT'),
        'DB_DATABASE' => getenv('DB_DATABASE')
    ],
    'file_permissions' => [
        'storage_dir' => substr(sprintf('%o', fileperms('/var/www/storage')), -4),
        'bootstrap_cache' => substr(sprintf('%o', fileperms('/var/www/bootstrap/cache')), -4)
    ],
    'components' => [
        'input-label_exists' => file_exists('/var/www/resources/views/components/input-label.blade.php')
    ]
];

// Test de la connexion MySQL
try {
    $pdo = new PDO(
        'mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    $serverInfo['database']['connection_test'] = 'success';
} catch (PDOException $e) {
    $serverInfo['database']['connection_test'] = 'failed';
    $serverInfo['database']['error'] = $e->getMessage();
}

echo json_encode($serverInfo, JSON_PRETTY_PRINT);
EOL
chmod 644 /var/www/public/server-info.php

# Créer un fichier de vérification de l'état
echo "OK" > /var/www/public/health
chmod 644 /var/www/public/health

log_success "====== DÉMARRAGE DE SUPERVISORD ======"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf