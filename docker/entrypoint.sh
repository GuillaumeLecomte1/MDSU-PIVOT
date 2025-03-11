#!/bin/bash
set -e

# Fonction pour vérifier si un fichier existe
check_file() {
    if [ ! -f "$1" ]; then
        echo "ERREUR: Le fichier $1 n'existe pas!"
        exit 1
    else
        echo "✅ Le fichier $1 existe."
    fi
}

# Fonction pour vérifier si un répertoire existe
check_dir() {
    if [ ! -d "$1" ]; then
        echo "ERREUR: Le répertoire $1 n'existe pas!"
        exit 1
    else
        echo "✅ Le répertoire $1 existe."
    fi
}

echo "🔍 Vérification de l'environnement..."

# Vérifier les fichiers et répertoires essentiels
check_file "/var/www/public/index.php"
check_dir "/var/www/storage"
check_dir "/var/www/public"

# Vérifier la configuration Nginx
check_file "/etc/nginx/sites-enabled/laravel.conf"
check_file "/var/log/nginx/error.log"
check_file "/var/log/nginx/access.log"

# Vérifier les permissions
echo "🔧 Configuration des permissions..."
chown -R www-data:www-data /var/www/storage
chmod -R 775 /var/www/storage

# Vérifier la configuration PHP-FPM
echo "🔧 Vérification de PHP-FPM..."
php-fpm -t

# Vérifier la configuration Nginx
echo "🔧 Vérification de Nginx..."
nginx -t

echo "✅ Environnement vérifié avec succès!"

# Exécuter la commande passée en argument (supervisord)
exec "$@" 