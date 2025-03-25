#!/bin/sh
set -e

echo "🚀 Starting Laravel application with Inertia.js and React..."
cd /var/www

# Create a static 500 error page
echo "📄 Creating static 500 error page"
cat > public/500.html << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <title>Service Temporarily Unavailable</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f8f9fa; }
        .container { text-align: center; max-width: 600px; padding: 20px; }
        h1 { font-size: 2.5em; margin-bottom: 16px; color: #e53935; }
        p { font-size: 1.1em; margin-bottom: 24px; }
        a { color: #3490dc; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .btn { display: inline-block; background: #3490dc; color: white; padding: 10px 20px; border-radius: 4px; font-weight: bold; transition: background 0.3s; }
        .btn:hover { background: #2779bd; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Service Temporarily Unavailable</h1>
        <p>The service is temporarily unavailable. We are working to resolve this issue.</p>
        <p>Please try again in a few moments.</p>
        <a href="/" class="btn">Return to Homepage</a>
    </div>
</body>
</html>
EOF

# Check if .env file exists, otherwise copy .env.example
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example"
    cp .env.example .env
    
    # Generate application key if needed
    echo "🔑 Generating application key"
    php artisan key:generate --force
else
    echo "✅ .env file found"
fi

# Ensure Vite manifest and assets are properly set up
echo "🔍 Checking Vite manifest and frontend assets..."
if [ ! -f public/build/manifest.json ] || [ ! -s public/build/manifest.json ]; then
    echo "⚠️ Vite manifest missing or invalid, creating fallback manifest"
    mkdir -p public/build/assets
    
    # Create simplified manifest that doesn't rely on 'src' or 'isEntry' properties
    cat > public/build/manifest.json << 'EOF'
{
    "resources/css/app.css": {
        "file": "assets/app.css"
    },
    "resources/js/app.jsx": {
        "file": "assets/app.js"
    }
}
EOF
    
    # Create fallback CSS and JS files if needed
    if [ ! -s public/build/assets/app.css ]; then
        echo "/* Fallback CSS */" > public/build/assets/app.css
    fi
    
    if [ ! -s public/build/assets/app.js ]; then
        echo "/* Fallback JS */" > public/build/assets/app.js
    fi
    
    # Modify Blade template to prevent Vite errors
    blade_files=$(find resources/views -type f -name "*.blade.php")
    for file in $blade_files; do
        # Check if the file contains @vite directive
        if grep -q "@vite" "$file"; then
            echo "⚠️ Modifying Vite directive in $file"
            sed -i 's/@vite(\[[^]]*\])/<script src="{{ asset('\''build\/assets\/app.js'\'') }}"><\/script><link rel="stylesheet" href="{{ asset('\''build\/assets\/app.css'\'') }}">/' "$file"
        fi
    done
else
    echo "✅ Valid Vite manifest found"
fi

# Create storage link if it doesn't exist
if [ ! -L public/storage ]; then
    echo "🔗 Creating storage symbolic link"
    php artisan storage:link --force
else
    echo "✅ Storage link found"
fi

# MySQL availability check is now handled in docker-compose, no need to wait here

# Clear all caches first
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Run database migrations (with error handling)
echo "🗄️ Running database migrations"
php artisan migrate --force || echo "⚠️ Migration failed, continuing..."

# Apply Production Optimizations
echo "⚡ Optimizing application for production"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Verify the compiled views don't contain problematic Vite references
echo "🔍 Final check of compiled views..."
if grep -q "vite(" storage/framework/views/*.php 2>/dev/null; then
    echo "⚠️ Vite references found in compiled views, clearing views cache..."
    php artisan view:clear
    
    # Replace the @vite directive in blade files
    find resources/views -type f -name "*.blade.php" -exec sed -i 's/@vite(\[[^]]*\])/<script src="{{ asset('\''build\/assets\/app.js'\'') }}"><\/script><link rel="stylesheet" href="{{ asset('\''build\/assets\/app.css'\'') }}">/' {} \;
    
    # Rebuild view cache
    php artisan view:cache
fi

# Set permissions
echo "🔒 Setting permissions"
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;
chown -R www-data:www-data storage bootstrap/cache

# Start supervisor to manage processes
echo "🚦 Starting services (nginx, php-fpm, queue)"
exec supervisord -c /etc/supervisord.conf