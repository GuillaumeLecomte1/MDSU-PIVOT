<?php

/**
 * Script pour corriger les URLs HTTP dans le manifeste Vite
 * Ce script vérifie le manifeste Vite et remplace toutes les URLs HTTP par des URLs HTTPS
 */

$manifestPath = '/var/www/public/build/manifest.json';
$viteManifestPath = '/var/www/public/build/.vite/manifest.json';

// Vérifier si le répertoire .vite existe
if (!is_dir('/var/www/public/build/.vite')) {
    echo "Le répertoire .vite n'existe pas. Création...\n";
    mkdir('/var/www/public/build/.vite', 0755, true);
}

// Fonction pour vérifier et corriger un manifeste
function checkAndFixManifest($path) {
    if (!file_exists($path)) {
        echo "Le fichier manifest.json n'existe pas à l'emplacement: $path\n";
        return false;
    }

    echo "Vérification du manifeste Vite pour les URLs HTTP à $path...\n";

    $manifest = json_decode(file_get_contents($path), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Erreur lors de la lecture du manifeste JSON: " . json_last_error_msg() . "\n";
        return false;
    }

    $modified = false;
    $httpCount = 0;

    // Fonction récursive pour parcourir le manifeste et remplacer les URLs HTTP par HTTPS
    function replaceHttpUrls(&$data) {
        global $modified, $httpCount;
        
        if (is_array($data)) {
            foreach ($data as $key => &$value) {
                if (is_string($value) && strpos($value, 'http://') === 0) {
                    $value = str_replace('http://', 'https://', $value);
                    $modified = true;
                    $httpCount++;
                    echo "URL HTTP remplacée: $value\n";
                } elseif (is_array($value)) {
                    replaceHttpUrls($value);
                }
            }
        }
    }

    replaceHttpUrls($manifest);

    if ($modified) {
        echo "Écriture du manifeste mis à jour avec $httpCount URLs corrigées...\n";
        file_put_contents($path, json_encode($manifest, JSON_PRETTY_PRINT));
        echo "Manifeste mis à jour avec succès!\n";
        return $manifest;
    } else {
        echo "Aucune URL HTTP trouvée dans le manifeste. Tout est déjà en HTTPS.\n";
        return $manifest;
    }
}

// Vérifier et corriger le manifeste principal
$mainManifest = checkAndFixManifest($manifestPath);

// Vérifier et corriger le manifeste dans .vite
$viteManifest = checkAndFixManifest($viteManifestPath);

// Si l'un des manifestes n'existe pas, le créer à partir de l'autre
if ($mainManifest && !$viteManifest) {
    echo "Copie du manifeste principal vers le répertoire .vite...\n";
    file_put_contents($viteManifestPath, json_encode($mainManifest, JSON_PRETTY_PRINT));
    echo "Manifeste copié avec succès.\n";
} elseif (!$mainManifest && $viteManifest) {
    echo "Copie du manifeste .vite vers le répertoire principal...\n";
    file_put_contents($manifestPath, json_encode($viteManifest, JSON_PRETTY_PRINT));
    echo "Manifeste copié avec succès.\n";
} elseif (!$mainManifest && !$viteManifest) {
    echo "Aucun manifeste trouvé. Création d'un manifeste minimal...\n";
    
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
    
    // Créer les répertoires nécessaires
    if (!is_dir('/var/www/public/build/assets/js')) {
        mkdir('/var/www/public/build/assets/js', 0755, true);
    }
    if (!is_dir('/var/www/public/build/assets/css')) {
        mkdir('/var/www/public/build/assets/css', 0755, true);
    }
    
    // Créer des fichiers vides si nécessaire
    if (!file_exists('/var/www/public/build/assets/js/app.js')) {
        touch('/var/www/public/build/assets/js/app.js');
    }
    if (!file_exists('/var/www/public/build/assets/css/app.css')) {
        touch('/var/www/public/build/assets/css/app.css');
    }
    
    // Écrire le manifeste dans les deux emplacements
    file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
    file_put_contents($viteManifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
    
    echo "Manifeste minimal créé avec succès.\n";
}

echo "Vérification terminée.\n"; 