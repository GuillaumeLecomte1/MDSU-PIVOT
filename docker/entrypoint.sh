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
log_info "Configuration des permissions..."

# Configuration des permissions en parallèle pour être plus rapide
(chmod -R 777 /var/www/storage) &
(chmod -R 777 /var/www/bootstrap/cache) &
(chmod -R 777 /dev/shm/laravel-logs) &
wait

# Définition de la propriété des fichiers
chown -R www-data:www-data /var/www
chown -R www-data:www-data /dev/shm/laravel-logs

# S'assurer que le fichier de log existe
touch /dev/shm/laravel-logs/laravel.log
chmod 666 /dev/shm/laravel-logs/laravel.log

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

# Informations de connexion à la base de données
log_info "====== INFORMATIONS DE CONNEXION BASE DE DONNÉES ======"
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
fi

# Nettoyage du cache
log_info "====== NETTOYAGE DU CACHE ======"
cd /var/www && php artisan config:clear
cd /var/www && php artisan view:clear
cd /var/www && php artisan route:clear
cd /var/www && php artisan optimize:clear

# Vérification des routes
log_info "====== VÉRIFICATION DES ROUTES ======"
cd /var/www && php artisan route:list --no-ansi

# Exécuter les migrations pour la base de données MySQL si spécifié et que la base de données est disponible
if [ "$RUN_MIGRATIONS" = "true" ] && [ "$connected" = true ]; then
    log_info "====== EXÉCUTION DES MIGRATIONS ======"
    cd /var/www && php artisan migrate --force
fi

# Créer un fichier de diagnostic
log_info "====== CRÉATION DU FICHIER DE DIAGNOSTIC ======"
cat > /var/www/public/server-info.php << 'EOL'
<?php
header('Content-Type: application/json');

// Start timer
$startTime = microtime(true);

// Server info
$serverInfo = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    'environment' => [
        'APP_ENV' => getenv('APP_ENV'),
        'APP_DEBUG' => getenv('APP_DEBUG'),
        'APP_URL' => getenv('APP_URL'),
    ],
    'server' => [
        'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'unknown',
    ],
    'memory_usage' => [
        'current' => memory_get_usage(true),
        'peak' => memory_get_peak_usage(true),
    ],
    'database' => [
        'DB_CONNECTION' => getenv('DB_CONNECTION'),
        'DB_HOST' => getenv('DB_HOST'),
        'DB_PORT' => getenv('DB_PORT'),
        'DB_DATABASE' => getenv('DB_DATABASE'),
    ],
    'storage' => [
        'permissions' => [
            'storage_dir' => substr(sprintf('%o', fileperms('/var/www/storage')), -4),
            'bootstrap_cache' => substr(sprintf('%o', fileperms('/var/www/bootstrap/cache')), -4),
        ]
    ]
];

// Test database connection
try {
    $startDb = microtime(true);
    $pdo = new PDO(
        'mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD'),
        [
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
    $query = $pdo->query('SELECT 1');
    $serverInfo['database']['connection_test'] = 'success';
    $serverInfo['database']['connection_time_ms'] = round((microtime(true) - $startDb) * 1000, 2);
    
    // Get tables count if connection succeeds
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $serverInfo['database']['tables_count'] = count($tables);
} catch (Exception $e) {
    $serverInfo['database']['connection_test'] = 'failed';
    $serverInfo['database']['error'] = $e->getMessage();
}

// Network tests
$serverInfo['network_tests'] = [];

// Test MySQL connection with hostname
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$startConn = microtime(true);
$conn = @fsockopen($host, $port, $errno, $errstr, 5);
if ($conn) {
    $serverInfo['network_tests']['mysql_tcp'] = [
        'status' => 'success',
        'time_ms' => round((microtime(true) - $startConn) * 1000, 2)
    ];
    fclose($conn);
} else {
    $serverInfo['network_tests']['mysql_tcp'] = [
        'status' => 'failed',
        'error' => "$errstr ($errno)"
    ];
}

// Include response time
$serverInfo['response_time_ms'] = round((microtime(true) - $startTime) * 1000, 2);

echo json_encode($serverInfo, JSON_PRETTY_PRINT);
EOL
chmod 644 /var/www/public/server-info.php

# Créer un fichier de vérification de l'état
cat > /var/www/public/health << 'EOL'
OK
EOL
chmod 644 /var/www/public/health

log_success "====== DÉMARRAGE DE SUPERVISORD ======"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf