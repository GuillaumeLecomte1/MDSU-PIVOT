#!/bin/bash

echo "Checking Vite manifest and assets..."

# Vérifier si le répertoire build existe
if [ ! -d "/var/www/public/build" ]; then
    echo "Creating build directory..."
    mkdir -p /var/www/public/build
fi

# Vérifier le manifest
if [ -f "/var/www/public/build/.vite/manifest.json" ]; then
    echo "Found manifest in .vite directory, copying..."
    cp /var/www/public/build/.vite/manifest.json /var/www/public/build/manifest.json
elif [ ! -f "/var/www/public/build/manifest.json" ]; then
    echo "❌ No manifest found!"
    exit 1
fi

# Vérifier les assets CSS
if [ ! -d "/var/www/public/build/assets/css" ]; then
    echo "Creating CSS directory..."
    mkdir -p /var/www/public/build/assets/css
fi

# Vérifier les permissions
echo "Setting correct permissions..."
chown -R www-data:www-data /var/www/public/build
find /var/www/public/build -type d -exec chmod 755 {} \;
find /var/www/public/build -type f -exec chmod 644 {} \;

echo "Manifest and assets check completed!" 