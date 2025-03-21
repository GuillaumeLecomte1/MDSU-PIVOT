#!/bin/bash
set -x

echo "====== DÉBUT DU DIAGNOSTIC DES PERMISSIONS ======"

# Vérification de l'existence des répertoires
echo "Vérification des répertoires..."
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/bootstrap/cache

# Force suppression de tous les fichiers de log existants
echo "Suppression de tous les fichiers de log existants..."
rm -f /var/www/storage/logs/*.log
rm -f /var/www/storage/logs/laravel-*.log

# Création du fichier de log avec les bonnes permissions
echo "Création d'un nouveau fichier de log..."
touch /var/www/storage/logs/laravel.log

# Application de permissions extrêmes
echo "Application de permissions extrêmes..."
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache
chmod 777 /var/www/storage/logs/laravel.log

# Vérification du propriétaire actuel
echo "Propriétaire actuel de storage/logs:"
ls -la /var/www/storage/logs

# Modification du propriétaire
echo "Modification du propriétaire..."
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chown www-data:www-data /var/www/storage/logs/laravel.log

# Vérification finale
echo "Propriétaire après modification:"
ls -la /var/www/storage/logs

# Vérification que www-data peut accéder au fichier
echo "Test d'écriture par www-data..."
su -s /bin/bash -c "echo 'Test write' > /var/www/storage/logs/laravel.log" www-data
cat /var/www/storage/logs/laravel.log

# Créer un fichier .htaccess pour protéger les logs si c'est accessible via le web
echo "Création d'un fichier .htaccess pour protéger les logs..."
echo "Order allow,deny" > /var/www/storage/logs/.htaccess
echo "Deny from all" >> /var/www/storage/logs/.htaccess

# Force la création de répertoires spécifiques pour Laravel 11
mkdir -p /var/www/storage/framework/down
mkdir -p /var/www/storage/framework/testing
mkdir -p /var/www/storage/framework/messages
chmod -R 777 /var/www/storage/framework/down
chmod -R 777 /var/www/storage/framework/testing
chmod -R 777 /var/www/storage/framework/messages

echo "====== FIN DU DIAGNOSTIC DES PERMISSIONS ======" 