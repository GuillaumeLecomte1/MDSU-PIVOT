#!/bin/bash
set -e

echo "====== DÉMARRAGE DU CONTENEUR ======"
echo "Configuration des permissions..."
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache
chown -R www-data:www-data /var/www

# Configure file-based cache instead of database cache
sed -i "s/CACHE_STORE=database/CACHE_STORE=file/g" /var/www/.env
sed -i "s/QUEUE_CONNECTION=database/QUEUE_CONNECTION=sync/g" /var/www/.env

# Wait for MySQL to be ready
echo "Waiting for MySQL connection..."
max_attempts=30
counter=0
until php -r "try { new PDO('mysql:host=127.0.0.1;dbname=pivot', 'root', '8ocxlumnakezr2wdfcwiijct2rejsgdr'); echo 'Connected to MySQL'; } catch (PDOException \$e) { echo \$e->getMessage(); exit(1); }" 2>/dev/null
do
  if [ $counter -eq $max_attempts ]; then
    echo "Failed to connect to MySQL after $max_attempts attempts. Continuing anyway..."
    break
  fi
  echo "MySQL connection attempt $((counter+1))/$max_attempts failed. Retrying in 2 seconds..."
  counter=$((counter+1))
  sleep 2
done

cd /var/www && php artisan config:clear
cd /var/www && php artisan view:clear
cd /var/www && php artisan route:clear
cd /var/www && php artisan optimize:clear

# Run migrations for MySQL database
cd /var/www && php artisan migrate --force || echo "Migration skipped"

echo "====== DÉMARRAGE DE SUPERVISORD ======"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf