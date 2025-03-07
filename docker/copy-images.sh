#!/bin/bash

# Ensure the script stops on first error
set -e

# Create necessary directories
mkdir -p /var/www/html/public/build/storage/imagesAccueil
mkdir -p /var/www/html/public/build/images

# Copy images from storage to public/build
echo "Copying images from storage to public/build..."
cp -r /var/www/html/storage/app/public/imagesAccueil/* /var/www/html/public/build/storage/imagesAccueil/
cp -r /var/www/html/public/images/* /var/www/html/public/build/images/

# Set correct permissions
echo "Setting correct permissions..."
chown -R www-data:www-data /var/www/html/public/build
chmod -R 755 /var/www/html/public/build

echo "Images have been copied successfully!" 