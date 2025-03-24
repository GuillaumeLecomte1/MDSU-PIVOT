#!/bin/bash
set -e

# Création du répertoire pour les images
mkdir -p /var/www/public/imagesAccueil

# Vérification si des images existent dans storage/app/public/imagesAccueil
if [ -d "/var/www/storage/app/public/imagesAccueil" ]; then
    echo "Copie des images depuis storage/app/public/imagesAccueil vers public/imagesAccueil"
    cp -r /var/www/storage/app/public/imagesAccueil/* /var/www/public/imagesAccueil/
fi

# S'assurer que les permissions sont correctes
chown -R www-data:www-data /var/www/public/imagesAccueil
chmod -R 755 /var/www/public/imagesAccueil

echo "Configuration des images terminée" 