#!/bin/bash
set -e

echo "🚀 Deploying Laravel application with Inertia.js and React..."

# Ensure .env exists
if [ ! -f .env ]; then
    echo "❌ Missing .env file! Please create one first."
    exit 1
fi

# Build frontend assets locally to avoid memory issues on the server
echo "📦 Building frontend assets..."
npm ci
npm run build

# Make sure the build was successful
if [ ! -f "public/build/manifest.json" ]; then
    echo "❌ Vite build failed! Please check your frontend code."
    exit 1
fi

# Prepare directories
echo "📁 Preparing directories..."
mkdir -p storage/app/public
mkdir -p storage/logs
mkdir -p storage/framework/{sessions,views,cache}
chmod -R 775 storage bootstrap/cache

# Build Docker image
echo "🐳 Building Docker image..."
docker build -t marketplace:latest .

# Save Docker image
echo "💾 Saving Docker image to file..."
docker save marketplace:latest | gzip > marketplace-image.tar.gz

# Deploy to VPS using dokploy (adjust as needed)
echo "🚀 Deploying to VPS using dokploy..."
dokploy up

echo "✅ Deployment completed successfully!"
echo "🌐 Your application should be available at http://pivot.guillaume-lcte.fr"
echo "   If you encounter any issues, check the logs with: docker logs laravel_app" 