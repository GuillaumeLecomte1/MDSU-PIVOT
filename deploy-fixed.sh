#!/bin/bash
set -e

echo "🚀 Deploying fixed Laravel application with Inertia.js and React..."

# Ensure we have necessary directories
mkdir -p storage/app/public
mkdir -p storage/logs
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Create a temporary directory for vite_fix
mkdir -p vite_fix

# Create local .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example"
    cp .env.example .env
    echo "Please update your .env file with your database credentials"
    echo "Then run this script again"
    exit 1
fi

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
echo "If you encounter any issues, you can run the following commands to troubleshoot:"
echo "docker-compose logs -f app     # Follow container logs"
echo "docker-compose exec app bash   # Access container shell"
echo "docker-compose exec app php artisan tinker # Run Laravel Tinker"
echo ""
echo "To apply the Vite class patch manually:"
echo "docker-compose exec app sh /var/www/vite_fix/apply_patch.sh" 