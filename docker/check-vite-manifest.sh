#!/bin/bash

# Script to check if the Vite manifest exists and generate it if needed

MANIFEST_PATH="/var/www/public/build/manifest.json"
echo "Checking for Vite manifest at $MANIFEST_PATH"

if [ -f "$MANIFEST_PATH" ]; then
    echo "✅ Vite manifest found at $MANIFEST_PATH"
    echo "Contents of manifest:"
    cat "$MANIFEST_PATH" | head -n 10
    echo "..."
else
    echo "❌ Vite manifest NOT found at $MANIFEST_PATH"
    echo "Checking build directory structure:"
    find /var/www/public/build -type f 2>/dev/null || echo "No files found in build directory"
    
    echo "Attempting to generate manifest..."
    cd /var/www
    
    # Check if node_modules exists
    if [ ! -d "node_modules" ]; then
        echo "Installing Node dependencies..."
        npm ci
    fi
    
    # Run the build
    echo "Running Vite build..."
    npm run build
    
    # Check if manifest was generated
    if [ -f "$MANIFEST_PATH" ]; then
        echo "✅ Vite manifest successfully generated at $MANIFEST_PATH"
        echo "Contents of manifest:"
        cat "$MANIFEST_PATH" | head -n 10
        echo "..."
        
        # Set proper permissions
        chown -R www-data:www-data /var/www/public/build
    else
        echo "❌ Failed to generate Vite manifest"
        echo "Current directory structure:"
        find /var/www/public -type f | grep -i build
    fi
fi 