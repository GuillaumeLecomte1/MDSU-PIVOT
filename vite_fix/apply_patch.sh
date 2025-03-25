#!/bin/bash
set -e

echo "🔧 Applying patch to Laravel Vite class to fix 'src' field issue..."

# Find Laravel's Vite class
VITE_PHP_PATH="/var/www/vendor/laravel/framework/src/Illuminate/Foundation/Vite.php"

if [ ! -f "$VITE_PHP_PATH" ]; then
    echo "❌ Could not find Laravel Vite class at $VITE_PHP_PATH"
    exit 1
fi

# First make a backup
cp "$VITE_PHP_PATH" "$VITE_PHP_PATH.backup"

# Apply the patch
if patch -N "$VITE_PHP_PATH" /var/www/vite_fix/Vite.php.patch; then
    echo "✅ Patch applied successfully!"
    
    # Restart PHP-FPM to apply changes
    if supervisorctl status php-fpm | grep -q "RUNNING"; then
        echo "🔄 Restarting PHP-FPM..."
        supervisorctl restart php-fpm
    fi
    
    # Clear cache
    echo "🧹 Clearing cache..."
    cd /var/www
    php artisan view:clear
    php artisan cache:clear
    
    echo "✅ Done! The Vite 'src' field issue should be fixed now."
else
    echo "❌ Failed to apply patch."
    # Restore backup
    mv "$VITE_PHP_PATH.backup" "$VITE_PHP_PATH"
    echo "✅ Original file restored."
fi 