#!/bin/bash
set -e

# Corriger les permissions au démarrage
echo "Préparation de l'environnement..."

# Vérification des répertoires
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/bootstrap/cache

# Permissions de stockage
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache

# Créer le fichier de log s'il n'existe pas
touch /var/www/storage/logs/laravel.log
chmod 666 /var/www/storage/logs/laravel.log

# Définir www-data comme propriétaire
chown -R www-data:www-data /var/www

echo "Permissions corrigées au démarrage."

# Lancer supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf 