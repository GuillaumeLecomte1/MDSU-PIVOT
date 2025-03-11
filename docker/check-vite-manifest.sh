#!/bin/bash

# Script pour vérifier le manifeste Vite
echo "Vérification du manifeste Vite..."

# Vérifier si le manifeste existe
if [ -f /var/www/public/build/manifest.json ]; then
    echo "✅ Le manifeste Vite existe."
    cat /var/www/public/build/manifest.json | head -n 10
else
    echo "❌ Le manifeste Vite n'existe pas."
    echo "Contenu du répertoire /var/www/public/build/ :"
    ls -la /var/www/public/build/ || echo "Le répertoire build n'existe pas."
fi

# Vérifier si le fichier index.php existe
if [ -f /var/www/public/index.php ]; then
    echo "✅ Le fichier index.php existe."
    ls -la /var/www/public/index.php
else
    echo "❌ Le fichier index.php n'existe pas."
    echo "Contenu du répertoire /var/www/public/ :"
    ls -la /var/www/public/
fi

exit 0 