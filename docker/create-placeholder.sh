#!/bin/bash

echo "Creating placeholder image..."

# Créer le répertoire si nécessaire
mkdir -p /var/www/public/images

# Vérifier si l'image existe déjà
if [ ! -f "/var/www/public/images/placeholder.jpg" ]; then
    # Créer une image placeholder avec ImageMagick si disponible
    if command -v convert &> /dev/null; then
        convert -size 300x300 canvas:lightgray -font Arial -pointsize 20 -gravity center -annotate 0 "Image non disponible" /var/www/public/images/placeholder.jpg
        echo "✅ Created placeholder image with ImageMagick"
    else
        # Sinon, créer un fichier texte qui sera servi comme image
        echo "ImageMagick not found, creating a simple placeholder file"
        echo "This is a placeholder image" > /var/www/public/images/placeholder.jpg
    fi
    
    # Définir les permissions
    chmod 644 /var/www/public/images/placeholder.jpg
    chown www-data:www-data /var/www/public/images/placeholder.jpg
    
    echo "✅ Placeholder image created at /var/www/public/images/placeholder.jpg"
else
    echo "✅ Placeholder image already exists"
fi

# Créer un lien symbolique dans le répertoire build si nécessaire
if [ ! -f "/var/www/public/build/images/placeholder.jpg" ]; then
    mkdir -p /var/www/public/build/images
    cp /var/www/public/images/placeholder.jpg /var/www/public/build/images/placeholder.jpg
    chmod 644 /var/www/public/build/images/placeholder.jpg
    chown www-data:www-data /var/www/public/build/images/placeholder.jpg
    echo "✅ Copied placeholder image to build directory"
fi

echo "Placeholder image setup completed!" 