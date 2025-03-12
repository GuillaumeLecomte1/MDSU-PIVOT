<?php

/**
 * Script pour vérifier et corriger les problèmes liés au manifeste Vite
 * 
 * Ce script vérifie si le manifeste Vite existe et le crée s'il est manquant
 */

// Chemin vers le répertoire public
$publicPath = '/var/www/public';

// Chemin vers le répertoire de build
$buildPath = $publicPath . '/build';

// Chemin vers le fichier manifeste
$manifestPath = $buildPath . '/manifest.json';

echo "Vérification du manifeste Vite...\n";

// Vérifier si le répertoire de build existe
if (!is_dir($buildPath)) {
    echo "Le répertoire de build n'existe pas. Création...\n";
    mkdir($buildPath, 0755, true);
}

// Vérifier si le fichier manifeste existe
if (!file_exists($manifestPath)) {
    echo "Le fichier manifest.json n'existe pas. Création d'un manifeste minimal...\n";
    
    // Créer un manifeste minimal
    $manifest = [
        "resources/js/app.jsx" => [
            "file" => "assets/js/app.js",
            "isEntry" => true,
            "src" => "resources/js/app.jsx"
        ],
        "resources/css/app.css" => [
            "file" => "assets/css/app.css",
            "isEntry" => true,
            "src" => "resources/css/app.css"
        ]
    ];
    
    // Écrire le manifeste dans le fichier
    file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
    
    echo "Manifeste minimal créé avec succès.\n";
    
    // Créer des fichiers vides pour les assets référencés
    if (!is_dir($buildPath . '/assets/js')) {
        mkdir($buildPath . '/assets/js', 0755, true);
    }
    
    if (!is_dir($buildPath . '/assets/css')) {
        mkdir($buildPath . '/assets/css', 0755, true);
    }
    
    // Créer des fichiers vides pour les assets
    touch($buildPath . '/assets/js/app.js');
    touch($buildPath . '/assets/css/app.css');
    
    echo "Fichiers d'assets vides créés.\n";
} else {
    echo "Le fichier manifest.json existe déjà.\n";
    
    // Vérifier si le manifeste est valide
    $manifestContent = file_get_contents($manifestPath);
    $manifest = json_decode($manifestContent, true);
    
    if ($manifest === null) {
        echo "Le manifeste existant n'est pas un JSON valide. Correction...\n";
        
        // Créer un manifeste minimal
        $manifest = [
            "resources/js/app.jsx" => [
                "file" => "assets/js/app.js",
                "isEntry" => true,
                "src" => "resources/js/app.jsx"
            ],
            "resources/css/app.css" => [
                "file" => "assets/css/app.css",
                "isEntry" => true,
                "src" => "resources/css/app.css"
            ]
        ];
        
        // Écrire le manifeste dans le fichier
        file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
        
        echo "Manifeste corrigé avec succès.\n";
    } else {
        echo "Le manifeste existant est valide.\n";
    }
}

echo "Vérification terminée.\n"; 