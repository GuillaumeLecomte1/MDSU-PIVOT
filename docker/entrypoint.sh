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

cd /var/www && php artisan config:clear
cd /var/www && php artisan view:clear
cd /var/www && php artisan route:clear
cd /var/www && php artisan optimize:clear

echo "====== DÉMARRAGE DE SUPERVISORD ======"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf