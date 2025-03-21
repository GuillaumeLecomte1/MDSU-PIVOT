#!/bin/bash
set -e

echo "====== DÉMARRAGE DU CONTENEUR ======"

# Préparation des répertoires et des permissions
echo "Configuration des répertoires et permissions..."
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/bootstrap/cache
mkdir -p /var/www/public/images

# Définir les permissions
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache
chown -R www-data:www-data /var/www

# Créer un fichier de log vide avec les bonnes permissions
touch /var/www/storage/logs/laravel.log
chmod 666 /var/www/storage/logs/laravel.log
chown www-data:www-data /var/www/storage/logs/laravel.log

# Configuration PHP pour les logs
echo "Configuration de PHP pour les logs..."
echo "log_errors = On" > /usr/local/etc/php/conf.d/error-log.ini
echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/error-log.ini

# Nettoyage des caches Laravel
echo "Nettoyage des caches Laravel..."
cd /var/www
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Informations sur le système
echo "Informations système:"
php -v
echo "====== DÉMARRAGE DE SUPERVISORD ======"

# Lancer supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf