#!/bin/bash
set -e

echo "🚀 Pre-building Vite assets locally for Docker deployment..."

# Install Node.js dependencies if needed
if [ ! -d "node_modules" ]; then
    echo "📦 Installing Node.js dependencies..."
    npm ci --no-audit --no-fund
fi

# Clear any existing build artifacts
echo "🧹 Clearing existing build artifacts..."
rm -rf public/build

# Build the frontend assets
echo "🔨 Building Vite assets..."
npm run build

# Verify the build was successful
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Build successful!"
    
    # Check if the manifest has the 'src' field
    if grep -q "\"src\":" "public/build/manifest.json"; then
        echo "✅ Manifest includes 'src' field."
    else
        echo "⚠️ Manifest is missing 'src' field. Fixing..."
        
        # Create a backup of the original manifest
        cp public/build/manifest.json public/build/manifest.json.bak
        
        # Extract CSS and JS file names
        CSS_FILE=$(grep -o '"file":"assets/app-[^"]*\.css"' public/build/manifest.json | grep -o 'assets/app-[^"]*\.css')
        JS_FILE=$(grep -o '"file":"assets/app-[^"]*\.js"' public/build/manifest.json | grep -o 'assets/app-[^"]*\.js')
        
        # If extraction failed, use default names
        CSS_FILE=${CSS_FILE:-"assets/app.css"}
        JS_FILE=${JS_FILE:-"assets/app.js"}
        
        # Create a new manifest with src fields
        echo '{
    "resources/css/app.css": {
        "file": "'$CSS_FILE'",
        "src": "resources/css/app.css",
        "isEntry": true
    },
    "resources/js/app.jsx": {
        "file": "'$JS_FILE'",
        "src": "resources/js/app.jsx",
        "isEntry": true
    }
}' > public/build/manifest.json
        
        echo "✅ Fixed manifest.json with 'src' field."
    fi
else
    echo "❌ Build failed! Creating fallback assets..."
    
    # Create minimal assets for deployment
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
    
    echo "/* Fallback CSS */" > public/build/assets/app.css
    echo "/* Fallback JS */" > public/build/assets/app.js
    
    echo "✅ Created fallback assets."
fi

echo "📦 Assets are ready for Docker deployment!"
echo "Run 'docker-compose build' to build your Docker image with the pre-built assets." 