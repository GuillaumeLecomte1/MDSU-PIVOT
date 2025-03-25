#!/bin/sh
set -e

echo "🚀 Starting Laravel application with Inertia.js and React..."
cd /var/www

# Show current PHP configuration
echo "🔍 PHP memory limit: $(php -r 'echo ini_get("memory_limit");')"

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
    php -d memory_limit=-1 artisan key:generate --force
else
    echo "✅ .env file found"
fi

# Patch the Laravel Vite class to fix the "src" field issue
echo "🔧 Patching Laravel Vite class to handle missing 'src' field..."
VITE_PHP_PATH="/var/www/vendor/laravel/framework/src/Illuminate/Foundation/Vite.php"

if [ -f "$VITE_PHP_PATH" ]; then
    # Backup first
    cp "$VITE_PHP_PATH" "$VITE_PHP_PATH.backup"
    
    # Apply the patch directly
    SEARCH='$path = $chunk['\''src'\''];'
    REPLACE='if (isset($chunk['\''src'\''])) { $path = $chunk['\''src'\'']; } else { $path = $file; }'
    
    # Use sed to replace the line
    sed -i "s/$SEARCH/$REPLACE/" "$VITE_PHP_PATH"
    echo "✅ Applied patch to Vite.php"
else
    echo "⚠️ Could not find Laravel Vite.php at $VITE_PHP_PATH"
fi

# Verify Vite assets
echo "🔍 Verifying Vite assets..."
if [ ! -f public/build/manifest.json ]; then
    echo "⚠️ Missing manifest.json, creating fallback..."
    mkdir -p public/build/assets
    
    # Create a basic manifest with src fields
    echo '{
    "resources/css/app.css": {
        "file": "assets/app.css",
        "src": "resources/css/app.css",
        "isEntry": true
    },
    "resources/js/app.jsx": {
        "file": "assets/app.js",
        "src": "resources/js/app.jsx",
        "isEntry": true
    }
}' > public/build/manifest.json
    
    # Create empty assets if needed
    touch public/build/assets/app.css
    touch public/build/assets/app.js
fi

# Create storage link if it doesn't exist
if [ ! -L public/storage ]; then
    echo "🔗 Creating storage symbolic link"
    php -d memory_limit=-1 artisan storage:link --force
else
    echo "✅ Storage link found"
fi

# Clear all caches first
echo "🧹 Clearing caches..."
php -d memory_limit=-1 artisan config:clear
php -d memory_limit=-1 artisan route:clear
php -d memory_limit=-1 artisan view:clear
php -d memory_limit=-1 artisan cache:clear

# Run database migrations (with error handling)
echo "🗄️ Running database migrations"
php -d memory_limit=-1 artisan migrate --force || echo "⚠️ Migration failed, continuing..."

# Apply Production Optimizations
echo "⚡ Optimizing application for production"
php -d memory_limit=-1 artisan config:cache
php -d memory_limit=-1 artisan route:cache
php -d memory_limit=-1 artisan view:cache
php -d memory_limit=-1 artisan optimize

# Check for and fix duplicate Larastan packages
if [ -d "vendor/nunomaduro/larastan" ] && [ -d "vendor/larastan/larastan" ]; then
    echo "⚠️ Found duplicate Larastan packages, fixing..."
    rm -rf vendor/nunomaduro/larastan
    echo "✅ Removed nunomaduro/larastan (deprecated)"
fi

# Set permissions
echo "🔒 Setting permissions"
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;
chown -R www-data:www-data storage bootstrap/cache
mkdir -p /var/log/supervisor
chown -R www-data:www-data /var/log/supervisor

# Start supervisor to manage processes
echo "🚦 Starting services (nginx, php-fpm, queue)"
exec supervisord -c /etc/supervisord.conf