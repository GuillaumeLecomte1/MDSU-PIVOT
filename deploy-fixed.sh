#!/bin/bash
set -e

echo "🚀 Deploying fixed Laravel application with Inertia.js and React..."

# Ensure we have necessary directories
mkdir -p storage/app/public
mkdir -p storage/logs
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Create local .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example"
    cp .env.example .env
    echo "Please update your .env file with your database credentials"
    echo "Then run this script again"
    exit 1
fi

# Clean up any potentially problematic issues
echo "🧹 Cleaning up potentially problematic issues..."
if [ -d "vendor/nunomaduro/larastan" ] && [ -d "vendor/larastan/larastan" ]; then
    echo "⚠️ Found duplicate Larastan packages in composer.json"
    echo "Consider removing nunomaduro/larastan from composer.json, it's deprecated"
fi

# Pre-build Vite assets to avoid issues in Docker
echo "🔨 Pre-building Vite assets locally..."
chmod +x prebuild-assets.sh
./prebuild-assets.sh

# Stop running containers
echo "🛑 Stopping running containers..."
docker-compose down || true

# Remove any existing images to ensure a clean build
echo "🧹 Removing existing images..."
docker rmi $(docker images -q laravel_app) 2>/dev/null || true

# Build the Docker image with proper debugging
echo "🐳 Building Docker image..."
docker-compose build --no-cache app

# Start the container
echo "🚀 Starting the container..."
docker-compose up -d

# Check container logs for any errors
echo "📋 Checking container logs..."
docker-compose logs app

echo "✅ Deployment complete!"
echo "Your application should be available at: http://pivot.guillaume-lcte.fr"
echo ""
echo "If you still encounter issues, you can try the following:"
echo ""
echo "1. Verify the Vite assets are working by checking:"
echo "   docker-compose exec app ls -la /var/www/public/build"
echo ""
echo "2. Manually fix the Vite.php file if needed:"
echo "   docker-compose exec app sh -c 'sed -i \"s/\\\$path = \\\$chunk[\\\'\\\''src\\\'\\\''];/if (isset(\\\$chunk[\\\'\\\''src\\\'\\\''])) { \\\$path = \\\$chunk[\\\'\\\''src\\\'\\\'\']; } else { \\\$path = \\\$file; }/\" /var/www/vendor/laravel/framework/src/Illuminate/Foundation/Vite.php'"
echo ""
echo "3. Manually clear the Laravel caches:"
echo "   docker-compose exec app php -d memory_limit=-1 artisan optimize:clear"
echo ""
echo "Additional debugging commands:"
echo "docker-compose logs -f app     # Follow container logs"
echo "docker-compose exec app bash   # Access container shell"
echo "docker-compose exec app php -d memory_limit=-1 artisan tinker # Run Laravel Tinker" 