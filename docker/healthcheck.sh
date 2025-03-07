#!/bin/bash

# Script to check the health of the application

echo "Checking application health..."

# Check if Nginx is running
if pgrep -x "nginx" > /dev/null; then
    echo "✅ Nginx is running"
else
    echo "❌ Nginx is not running"
    exit 1
fi

# Check if PHP-FPM is running
if pgrep -x "php-fpm" > /dev/null; then
    echo "✅ PHP-FPM is running"
else
    echo "❌ PHP-FPM is not running"
    exit 1
fi

# Check if the Vite manifest exists
MANIFEST_PATH="/var/www/public/build/manifest.json"
VITE_MANIFEST_PATH="/var/www/public/build/.vite/manifest.json"

if [ -f "$MANIFEST_PATH" ]; then
    echo "✅ Vite manifest exists at $MANIFEST_PATH"
elif [ -f "$VITE_MANIFEST_PATH" ]; then
    echo "✅ Vite manifest exists at $VITE_MANIFEST_PATH, copying to $MANIFEST_PATH"
    cp "$VITE_MANIFEST_PATH" "$MANIFEST_PATH"
    echo "✅ Copied manifest to $MANIFEST_PATH"
else
    echo "❌ Vite manifest does not exist at $MANIFEST_PATH or $VITE_MANIFEST_PATH"
    
    # Try to fix the issue
    echo "Attempting to fix Vite manifest issue..."
    cd /var/www && php docker/fix-vite-issues.php
    
    # Check again
    if [ -f "$MANIFEST_PATH" ]; then
        echo "✅ Vite manifest issue fixed"
    else
        echo "❌ Failed to fix Vite manifest issue"
        exit 1
    fi
fi

# Check if Laravel is properly configured
cd /var/www && php artisan --version
if [ $? -eq 0 ]; then
    echo "✅ Laravel is properly configured"
else
    echo "❌ Laravel is not properly configured"
    exit 1
fi

echo "Application health check completed successfully!"
exit 0 