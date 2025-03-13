<?php

/**
 * Script pour corriger les URLs HTTP en HTTPS dans le manifeste Vite
 * Ce script vérifie si le manifeste Vite existe et remplace toutes les URLs HTTP par HTTPS
 */

echo "Vérification des URLs dans le manifeste Vite...\n";

// Chemin vers le manifeste Vite
$manifestPath = '/var/www/public/build/manifest.json';

// Vérifier si le manifeste existe
if (!file_exists($manifestPath)) {
    echo "Le manifeste Vite n'existe pas à l'emplacement: $manifestPath\n";
    exit(1);
}

// Lire le contenu du manifeste
$manifestContent = file_get_contents($manifestPath);

// Vérifier si le contenu est un JSON valide
$manifest = json_decode($manifestContent, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Erreur lors de la lecture du manifeste: " . json_last_error_msg() . "\n";
    exit(1);
}

// Fonction récursive pour remplacer les URLs HTTP par HTTPS
function replaceHttpWithHttps(&$array) {
    $modified = false;
    
    if (is_array($array)) {
        foreach ($array as $key => &$value) {
            if (is_string($value) && strpos($value, 'http:') !== false) {
                $value = str_replace('http:', 'https:', $value);
                $modified = true;
                echo "URL modifiée: $value\n";
            } elseif (is_array($value)) {
                $innerModified = replaceHttpWithHttps($value);
                $modified = $modified || $innerModified;
            }
        }
    }
    
    return $modified;
}

// Remplacer les URLs HTTP par HTTPS dans le manifeste
$modified = replaceHttpWithHttps($manifest);

// Si des modifications ont été effectuées, écrire le nouveau manifeste
if ($modified) {
    echo "Des URLs HTTP ont été trouvées et remplacées par HTTPS.\n";
    $newManifestContent = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents($manifestPath, $newManifestContent);
    echo "Manifeste mis à jour avec succès.\n";
} else {
    echo "Aucune URL HTTP trouvée dans le manifeste.\n";
}

// Vérifier également les fichiers JS et CSS générés
$buildDir = '/var/www/public/build';
$jsDir = "$buildDir/assets/js";
$cssDir = "$buildDir/assets/css";

// Fonction pour scanner un répertoire et remplacer les URLs HTTP par HTTPS dans les fichiers
function scanDirectoryAndReplaceUrls($dir, $extensions) {
    if (!is_dir($dir)) {
        echo "Le répertoire $dir n'existe pas.\n";
        return;
    }
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $filePath = "$dir/$file";
        if (is_file($filePath)) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            if (in_array($extension, $extensions)) {
                $content = file_get_contents($filePath);
                $newContent = str_replace('http:', 'https:', $content);
                
                if ($content !== $newContent) {
                    echo "URLs HTTP trouvées et remplacées dans $filePath\n";
                    file_put_contents($filePath, $newContent);
                }
            }
        }
    }
}

// Remplacer les URLs HTTP par HTTPS dans les fichiers JS et CSS
echo "Vérification des fichiers JS...\n";
scanDirectoryAndReplaceUrls($jsDir, ['js']);

echo "Vérification des fichiers CSS...\n";
scanDirectoryAndReplaceUrls($cssDir, ['css']);

echo "Vérification terminée.\n"; 