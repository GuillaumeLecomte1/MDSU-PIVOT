#!/bin/sh
set -e

echo "🚀 Starting Laravel application with Inertia.js and React..."
cd /var/www

# Show current PHP configuration
echo "🔍 PHP memory limit: $(php -r 'echo ini_get("memory_limit");')"
echo "🔍 Disabled functions: $(php -r 'echo ini_get("disable_functions");')"
echo "🔍 Node memory limit: $NODE_OPTIONS"

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

# Check and install missing npm dependencies
echo "📦 Checking Node.js dependencies..."
MISSING_DEPS=""

# Check for required dependencies
for DEP in lodash react-markdown react-syntax-highlighter remark-gfm react react-dom @inertiajs/react axios; do
    if ! npm list $DEP >/dev/null 2>&1; then
        echo "⚠️ $DEP not found, will be installed..."
        MISSING_DEPS="$MISSING_DEPS $DEP"
    fi
done

# Install missing dependencies if any
if [ ! -z "$MISSING_DEPS" ]; then
    echo "📦 Installing missing dependencies: $MISSING_DEPS"
    npm install $MISSING_DEPS --save --legacy-peer-deps
fi

# Verify Vite assets and build if needed
echo "🔍 Verifying Vite assets..."
if [ ! -f public/build/manifest.json ] || [ ! -s public/build/manifest.json ]; then
    echo "⚠️ Vite assets not found or empty manifest, attempting to build..."
    
    # Make sure package.json and package-lock.json are in sync
    if [ -f package-lock.json ]; then
        echo "📦 Updating package-lock.json to match package.json..."
        rm -f package-lock.json
    fi
    
    # Install dependencies with npm install
    echo "📦 Installing Node.js dependencies..."
    npm install --no-audit --no-fund --legacy-peer-deps
    
    echo "🔨 Building Vite assets with increased timeout..."
    NODE_OPTIONS=--max-old-space-size=4096 npm run build || {
        echo "⚠️ Vite build failed, creating fallback assets..."
        mkdir -p public/build/assets
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
        
        # Create empty assets if they don't exist
        [ -f public/build/assets/app.css ] || echo "/* Fallback CSS */" > public/build/assets/app.css
        [ -f public/build/assets/app.js ] || echo "/* Fallback JS - Built by Docker */" > public/build/assets/app.js
        echo "✅ Created fallback Vite manifest and assets"
    }
else
    echo "✅ Vite assets found"
fi

# Run the Vite patch script if available
if [ -f "vite-patch.php" ]; then
    echo "🔧 Running Vite patch script..."
    php vite-patch.php
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