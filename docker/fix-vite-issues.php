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

// Chemin vers le manifeste Vite dans le répertoire .vite
$viteManifestPath = $buildPath . '/.vite/manifest.json';

echo "Vérification du manifeste Vite...\n";

// Vérifier si le répertoire de build existe
if (!is_dir($buildPath)) {
    echo "Le répertoire de build n'existe pas. Création...\n";
    mkdir($buildPath, 0755, true);
}

// Vérifier si le manifeste existe dans le répertoire .vite
if (file_exists($viteManifestPath)) {
    echo "Manifeste trouvé dans le répertoire .vite. Copie vers le répertoire racine de build...\n";
    
    // Copier le manifeste
    copy($viteManifestPath, $manifestPath);
    
    echo "Manifeste copié avec succès.\n";
} 
// Vérifier si le fichier manifeste existe dans le répertoire racine
elseif (!file_exists($manifestPath)) {
    echo "Le fichier manifest.json n'existe pas. Création d'un manifeste minimal...\n";
    
    // Vérifier si des assets ont été générés
    $assetsDir = $buildPath . '/assets';
    $jsDir = $assetsDir . '/js';
    $cssDir = $assetsDir . '/css';
    
    // Créer un manifeste basé sur les fichiers existants
    $manifest = [];
    
    // Ajouter les entrées JS
    if (is_dir($jsDir)) {
        $jsFiles = glob($jsDir . '/app-*.js');
        if (!empty($jsFiles)) {
            $jsFile = basename(reset($jsFiles));
            $manifest["resources/js/app.jsx"] = [
                "file" => "assets/js/{$jsFile}",
                "isEntry" => true,
                "src" => "resources/js/app.jsx"
            ];
        } else {
            // Aucun fichier JS trouvé, utiliser une entrée par défaut
            $manifest["resources/js/app.jsx"] = [
                "file" => "assets/js/app.js",
                "isEntry" => true,
                "src" => "resources/js/app.jsx"
            ];
            
            // Créer le répertoire et un fichier vide
            if (!is_dir($jsDir)) {
                mkdir($jsDir, 0755, true);
            }
            touch($jsDir . '/app.js');
        }
    } else {
        // Aucun répertoire JS trouvé, utiliser une entrée par défaut
        $manifest["resources/js/app.jsx"] = [
            "file" => "assets/js/app.js",
            "isEntry" => true,
            "src" => "resources/js/app.jsx"
        ];
        
        // Créer le répertoire et un fichier vide
        mkdir($jsDir, 0755, true);
        touch($jsDir . '/app.js');
    }
    
    // Ajouter les entrées CSS
    if (is_dir($cssDir)) {
        $cssFiles = glob($cssDir . '/app-*.css');
        if (!empty($cssFiles)) {
            $cssFile = basename(reset($cssFiles));
            $manifest["resources/css/app.css"] = [
                "file" => "assets/css/{$cssFile}",
                "isEntry" => true,
                "src" => "resources/css/app.css"
            ];
        } else {
            // Aucun fichier CSS trouvé, utiliser une entrée par défaut
            $manifest["resources/css/app.css"] = [
                "file" => "assets/css/app.css",
                "isEntry" => true,
                "src" => "resources/css/app.css"
            ];
            
            // Créer le répertoire et un fichier vide
            if (!is_dir($cssDir)) {
                mkdir($cssDir, 0755, true);
            }
            touch($cssDir . '/app.css');
        }
    } else {
        // Aucun répertoire CSS trouvé, utiliser une entrée par défaut
        $manifest["resources/css/app.css"] = [
            "file" => "assets/css/app.css",
            "isEntry" => true,
            "src" => "resources/css/app.css"
        ];
        
        // Créer le répertoire et un fichier vide
        mkdir($cssDir, 0755, true);
        touch($cssDir . '/app.css');
    }
    
    // Écrire le manifeste dans le fichier
    file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
    
    echo "Manifeste minimal créé avec succès.\n";
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