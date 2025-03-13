<?php

/**
 * Script pour corriger les problèmes de Mixed Content dans les fichiers compilés
 * Ce script parcourt tous les fichiers JS et CSS dans le répertoire build et remplace les URLs HTTP par HTTPS
 */

echo "Correction des problèmes de Mixed Content dans les fichiers compilés...\n";

// Répertoire des assets compilés
$buildDir = '/var/www/public/build';

// Vérifier si le répertoire build existe
if (!is_dir($buildDir)) {
    echo "Le répertoire build n'existe pas à l'emplacement: $buildDir\n";
    exit(1);
}

// Fonction pour scanner récursivement un répertoire et traiter les fichiers
function scanDirectoryRecursively($dir, $callback) {
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $path = "$dir/$file";
        
        if (is_dir($path)) {
            scanDirectoryRecursively($path, $callback);
        } else {
            $callback($path);
        }
    }
}

// Fonction pour remplacer les URLs HTTP par HTTPS dans un fichier
function replaceHttpWithHttps($filePath) {
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    
    // Traiter uniquement les fichiers JS, CSS, HTML, JSON et SVG
    if (!in_array($extension, ['js', 'css', 'html', 'json', 'svg'])) {
        return;
    }
    
    $content = file_get_contents($filePath);
    
    // Patterns à rechercher et remplacer
    $patterns = [
        'http://' => 'https://',
        'ws://' => 'wss://',
        'url(http:' => 'url(https:',
        'url("http:' => 'url("https:',
        "url('http:" => "url('https:",
        'href="http:' => 'href="https:',
        "href='http:" => "href='https:",
        'src="http:' => 'src="https:',
        "src='http:" => "src='https:",
        'action="http:' => 'action="https:',
        "action='http:" => "action='https:",
    ];
    
    $modified = false;
    $newContent = $content;
    
    foreach ($patterns as $search => $replace) {
        if (strpos($newContent, $search) !== false) {
            $newContent = str_replace($search, $replace, $newContent);
            $modified = true;
        }
    }
    
    if ($modified) {
        echo "URLs HTTP trouvées et remplacées dans $filePath\n";
        file_put_contents($filePath, $newContent);
    }
}

// Parcourir tous les fichiers dans le répertoire build et remplacer les URLs HTTP par HTTPS
echo "Parcours des fichiers dans le répertoire build...\n";
scanDirectoryRecursively($buildDir, 'replaceHttpWithHttps');

echo "Correction des problèmes de Mixed Content terminée.\n"; 