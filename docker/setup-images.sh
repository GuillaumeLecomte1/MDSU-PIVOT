#!/bin/bash
set -e

# Création du répertoire pour les images
mkdir -p /var/www/public/imagesAccueil

# Vérification si des images existent dans storage/app/public/imagesAccueil
if [ -d "/var/www/storage/app/public/imagesAccueil" ]; then
    echo "Copie des images depuis storage/app/public/imagesAccueil vers public/imagesAccueil"
    cp -f /var/www/storage/app/public/imagesAccueil/* /var/www/public/imagesAccueil/ || true
fi

# Création de fichiers vides si les images n'existent pas pour satisfaire Vite
for img in imageAccueil1.png Calque_1.svg dernierArrivage.png aPropos.png blog1.png blog2.png; do
    if [ ! -f "/var/www/public/imagesAccueil/$img" ]; then
        echo "Création d'un fichier vide pour $img"
        touch "/var/www/public/imagesAccueil/$img"
    fi
done

# Modification du Welcome.jsx pour utiliser un chemin qui fonctionnera avec Vite
if [ -f "/var/www/resources/js/Pages/Welcome.jsx" ]; then
    echo "Modification des chemins dans Welcome.jsx"
    sed -i 's|\/imagesAccueil\/|\/public\/imagesAccueil\/|g' /var/www/resources/js/Pages/Welcome.jsx || true
fi

# S'assurer que les permissions sont correctes
chown -R www-data:www-data /var/www/public/imagesAccueil
chmod -R 755 /var/www/public/imagesAccueil

echo "Configuration des images terminée" 