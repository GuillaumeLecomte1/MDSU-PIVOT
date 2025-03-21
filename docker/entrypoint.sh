#!/bin/bash
set -e

echo "====== DÉMARRAGE DU CONTENEUR ======"
echo "Configuration des permissions..."
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache
chown -R www-data:www-data /var/www

cd /var/www && php artisan config:clear
cd /var/www && php artisan view:clear
cd /var/www && php artisan route:clear
cd /var/www && php artisan optimize:clear

echo "====== DÉMARRAGE DE SUPERVISORD ======"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf