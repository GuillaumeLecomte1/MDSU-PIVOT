#!/bin/bash

# Script pour corriger les fichiers statiques
echo "Vérification des fichiers statiques..."

# Vérifier si le dossier images existe
if [ ! -d /var/www/public/images ]; then
    echo "Création du dossier images..."
    mkdir -p /var/www/public/images
    chown -R www-data:www-data /var/www/public/images
fi

# Vérifier si l'image placeholder.jpg existe
if [ ! -f /var/www/public/images/placeholder.jpg ]; then
    echo "Création de l'image placeholder.jpg..."
    touch /var/www/public/images/placeholder.jpg
    chown www-data:www-data /var/www/public/images/placeholder.jpg
fi

# Vérifier si le lien symbolique storage existe
if [ ! -L /var/www/public/storage ]; then
    echo "Création du lien symbolique storage..."
    cd /var/www && php artisan storage:link
fi

# Vérifier les permissions
echo "Vérification des permissions..."
chown -R www-data:www-data /var/www/storage
find /var/www/storage -type d -exec chmod 775 {} \;
find /var/www/storage -type f -exec chmod 664 {} \;

echo "✅ Vérification des fichiers statiques terminée."
exit 0 