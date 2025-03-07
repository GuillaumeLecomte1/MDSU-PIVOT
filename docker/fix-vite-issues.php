<?php

/**
 * Script to fix common Vite issues in production
 */

// Define paths
$manifestPath = __DIR__ . '/../public/build/manifest.json';
$buildDir = __DIR__ . '/../public/build';

echo "Starting Vite issues fix script...\n";

// Check if build directory exists
if (!is_dir($buildDir)) {
    echo "Creating build directory...\n";
    mkdir($buildDir, 0755, true);
}

// Check if manifest exists
if (!file_exists($manifestPath)) {
    echo "Manifest not found. Creating a basic manifest...\n";
    
    // Create a basic manifest
    $manifest = [
        "resources/js/app.jsx" => [
            "file" => "assets/app.js",
            "src" => "resources/js/app.jsx",
            "isEntry" => true,
            "css" => ["assets/app.css"]
        ],
        "resources/css/app.css" => [
            "file" => "assets/app.css",
            "src" => "resources/css/app.css",
            "isEntry" => true
        ]
    ];
    
    // Write the manifest
    file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
    
    echo "Basic manifest created. You should rebuild your assets properly.\n";
} else {
    echo "Manifest found. Checking content...\n";
    
    $manifest = json_decode(file_get_contents($manifestPath), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error parsing manifest: " . json_last_error_msg() . "\n";
        echo "Recreating manifest...\n";
        
        // Create a basic manifest
        $manifest = [
            "resources/js/app.jsx" => [
                "file" => "assets/app.js",
                "src" => "resources/js/app.jsx",
                "isEntry" => true,
                "css" => ["assets/app.css"]
            ],
            "resources/css/app.css" => [
                "file" => "assets/app.css",
                "src" => "resources/css/app.css",
                "isEntry" => true
            ]
        ];
        
        // Write the manifest
        file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
        
        echo "Basic manifest created. You should rebuild your assets properly.\n";
    } else {
        echo "Manifest is valid.\n";
    }
}

// Check Laravel configuration
echo "Checking Laravel configuration...\n";
echo "Using environment variables provided by Dokploy.\n";

echo "Script completed.\n"; 