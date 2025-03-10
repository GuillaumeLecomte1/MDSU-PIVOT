#!/bin/bash

echo "Checking and fixing image issues..."

# Vérifier les répertoires d'images
DIRS_TO_CHECK=(
    "/var/www/public/images"
    "/var/www/public/build/images"
    "/var/www/public/build/assets/images"
    "/var/www/public/storage/imagesAccueil"
    "/var/www/storage/app/public/imagesAccueil"
)

# Créer les répertoires s'ils n'existent pas
for dir in "${DIRS_TO_CHECK[@]}"; do
    if [ ! -d "$dir" ]; then
        echo "Creating directory: $dir"
        mkdir -p "$dir"
        chown -R www-data:www-data "$dir"
        chmod -R 755 "$dir"
    else
        echo "✅ Directory exists: $dir"
    fi
done

# Vérifier l'image placeholder
PLACEHOLDER_PATHS=(
    "/var/www/public/images/placeholder.jpg"
    "/var/www/public/build/images/placeholder.jpg"
)

for path in "${PLACEHOLDER_PATHS[@]}"; do
    if [ ! -f "$path" ]; then
        echo "Creating placeholder image at: $path"
        # Créer une image placeholder avec ImageMagick si disponible
        if command -v convert &> /dev/null; then
            convert -size 300x300 canvas:lightgray -font Arial -pointsize 20 -gravity center -annotate 0 "Image non disponible" "$path"
        else
            # Sinon, créer un fichier texte
            echo "This is a placeholder image" > "$path"
        fi
        chmod 644 "$path"
        chown www-data:www-data "$path"
    else
        echo "✅ Placeholder image exists at: $path"
    fi
done

# Copier les images d'un répertoire à l'autre si nécessaire
echo "Copying images between directories..."

# Copier les images de storage vers public
if [ -d "/var/www/storage/app/public/imagesAccueil" ]; then
    echo "Copying images from storage to public..."
    mkdir -p /var/www/public/storage/imagesAccueil
    cp -r /var/www/storage/app/public/imagesAccueil/* /var/www/public/storage/imagesAccueil/ 2>/dev/null || true
    chmod -R 644 /var/www/public/storage/imagesAccueil/*
    chown -R www-data:www-data /var/www/public/storage/imagesAccueil
fi

# Copier les images vers le répertoire build
if [ -d "/var/www/public/images" ]; then
    echo "Copying images to build directory..."
    mkdir -p /var/www/public/build/images
    cp -r /var/www/public/images/* /var/www/public/build/images/ 2>/dev/null || true
    chmod -R 644 /var/www/public/build/images/*
    chown -R www-data:www-data /var/www/public/build/images
fi

# Vérifier les liens symboliques
if [ ! -L "/var/www/public/storage" ] && [ -d "/var/www/storage/app/public" ]; then
    echo "Creating storage symlink..."
    ln -sf /var/www/storage/app/public /var/www/public/storage
fi

echo "Image fixes completed!" 