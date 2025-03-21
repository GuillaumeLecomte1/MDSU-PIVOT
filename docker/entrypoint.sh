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

# Ensure SQLite database exists
mkdir -p /var/www/database
touch /var/www/database/database.sqlite
chmod 777 /var/www/database/database.sqlite

cd /var/www && php artisan config:clear
cd /var/www && php artisan view:clear
cd /var/www && php artisan route:clear
cd /var/www && php artisan optimize:clear

# Run migrations for SQLite database
cd /var/www && php artisan migrate --force || echo "Migration skipped"

echo "====== DÉMARRAGE DE SUPERVISORD ======"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf