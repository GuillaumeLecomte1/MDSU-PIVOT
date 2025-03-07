#!/bin/bash

# Script to check and fix static files access issues

echo "Checking static files access..."

# Check if the public directory exists
if [ -d "/var/www/public" ]; then
    echo "✅ Public directory exists"
else
    echo "❌ Public directory does not exist"
    exit 1
fi

# Set proper permissions for all public files
echo "Setting proper permissions for all public files..."
find /var/www/public -type d -exec chmod 755 {} \;
find /var/www/public -type f -exec chmod 644 {} \;
chown -R www-data:www-data /var/www/public

# Check for common image directories
for dir in "/var/www/public/images" "/var/www/public/img" "/var/www/public/assets"; do
    if [ -d "$dir" ]; then
        echo "✅ Found image directory: $dir"
        echo "Setting permissions for $dir..."
        find "$dir" -type d -exec chmod 755 {} \;
        find "$dir" -type f -exec chmod 644 {} \;
        chown -R www-data:www-data "$dir"
    fi
done

# List all image files in public directory
echo "Listing all image files in public directory..."
find /var/www/public -type f -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" -o -name "*.gif" -o -name "*.svg" -o -name "*.webp" | head -n 20

# Check Nginx configuration
echo "Checking Nginx configuration..."
nginx -t

echo "Static files access check completed!"
exit 0 