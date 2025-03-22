#!/bin/bash
set -e

echo "====== DÉMARRAGE DU CONTENEUR ======"
echo "Configuration des permissions..."
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache
chmod -R 777 /dev/shm/laravel-logs
chown -R www-data:www-data /var/www
chown -R www-data:www-data /dev/shm/laravel-logs
touch /dev/shm/laravel-logs/laravel.log
chmod 666 /dev/shm/laravel-logs/laravel.log

# Network diagnostics
echo "====== DIAGNOSTIC RÉSEAU ======"
if command -v dig &> /dev/null; then
    echo "Performing DNS lookup for MySQL:"
    dig mysql
fi

if command -v getent &> /dev/null; then
    echo "Looking up hosts for MySQL:"
    getent hosts mysql
fi

# Add MySQL host to /etc/hosts if needed
if ! grep -q "mysql" /etc/hosts; then
    echo "Adding MySQL to /etc/hosts..."
    echo "# Ensuring MySQL connectivity" >> /etc/hosts
    echo "10.0.1.50 mysql" >> /etc/hosts
fi

# Configure file-based cache instead of database cache
sed -i "s/CACHE_STORE=database/CACHE_STORE=file/g" /var/www/.env
sed -i "s/QUEUE_CONNECTION=database/QUEUE_CONNECTION=sync/g" /var/www/.env

# Database connection information
echo "====== INFORMATIONS DE CONNEXION BASE DE DONNÉES ======"
DB_HOST=$(grep DB_HOST /var/www/.env | cut -d= -f2)
DB_PORT=$(grep DB_PORT /var/www/.env | cut -d= -f2)
DB_NAME=$(grep DB_DATABASE /var/www/.env | cut -d= -f2)
DB_USER=$(grep DB_USERNAME /var/www/.env | cut -d= -f2)
echo "Database Host: $DB_HOST"
echo "Database Port: $DB_PORT"
echo "Database Name: $DB_NAME"
echo "Database User: $DB_USER"

# Ensure MySQL is accessible
if command -v telnet &> /dev/null; then
    echo "Testing MySQL connectivity with telnet (quick check, will timeout if not available)..."
    timeout 5 telnet $DB_HOST $DB_PORT || echo "Could not connect to MySQL with telnet, continuing anyway..."
fi

# Wait for MySQL to be ready
echo "Waiting for MySQL connection..."
max_attempts=30
counter=0
until php -r "try { new PDO('mysql:host=$DB_HOST;dbname=$DB_NAME', '$DB_USER', '8ocxlumnakezr2wdfcwiijct2rejsgdr'); echo 'Connected to MySQL'; } catch (PDOException \$e) { echo \$e->getMessage(); exit(1); }" 2>/dev/null
do
  if [ $counter -eq $max_attempts ]; then
    echo "Failed to connect to MySQL after $max_attempts attempts. Continuing anyway..."
    break
  fi
  echo "MySQL connection attempt $((counter+1))/$max_attempts failed. Retrying in 2 seconds..."

  counter=$((counter+1))
  sleep 2
done

echo "====== NETTOYAGE DU CACHE ======"
cd /var/www && php artisan config:clear
cd /var/www && php artisan view:clear
cd /var/www && php artisan route:clear
cd /var/www && php artisan optimize:clear

echo "====== VÉRIFICATION DES ROUTES ======"
cd /var/www && php artisan route:list --no-ansi

# Run migrations for MySQL database if specified and DB is available
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "====== EXÉCUTION DES MIGRATIONS ======"
    cd /var/www && php artisan migrate --force
fi

# Create a health check file
echo "====== CRÉATION DU FICHIER DE DIAGNOSTIC ======"
cat > /var/www/public/server-info.php << 'EOL'
<?php
header('Content-Type: application/json');
$info = [
    'timestamp' => date('Y-m-d H:i:s'),
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
    'database' => [
        'DB_CONNECTION' => getenv('DB_CONNECTION'),
        'DB_HOST' => getenv('DB_HOST'),
        'DB_PORT' => getenv('DB_PORT'),
        'DB_DATABASE' => getenv('DB_DATABASE'),
    ],
];

try {
    $pdo = new PDO(
        'mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    $info['database']['connection_test'] = 'success';
} catch (Exception $e) {
    $info['database']['connection_test'] = 'failed';
    $info['database']['error'] = $e->getMessage();
}

echo json_encode($info, JSON_PRETTY_PRINT);
EOL
chmod 644 /var/www/public/server-info.php

echo "====== DÉMARRAGE DE SUPERVISORD ======"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf