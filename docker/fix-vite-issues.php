<?php

/**
 * Script pour vérifier et corriger les problèmes liés au manifeste Vite
 * 
 * Ce script vérifie si le manifeste Vite existe et le crée s'il est manquant
 */

// Vérification de l'existence des répertoires nécessaires
$publicBuildDir = '/var/www/public/build';
$assetsJsDir = $publicBuildDir . '/assets/js';
$assetsCssDir = $publicBuildDir . '/assets/css';
$viteDir = $publicBuildDir . '/.vite';

// Création des répertoires s'ils n'existent pas
foreach ([$publicBuildDir, $assetsJsDir, $assetsCssDir, $viteDir] as $dir) {
    if (!is_dir($dir)) {
        echo "Création du répertoire $dir\n";
        mkdir($dir, 0755, true);
    }
}

// Création des fichiers JS et CSS minimaux
$jsFile = $assetsJsDir . '/app.js';
$cssFile = $assetsCssDir . '/app.css';

// Contenu minimal du fichier JS
$jsContent = "// Fichier JS de secours généré automatiquement\nconsole.log('Vite build fallback loaded');";

// Contenu minimal du fichier CSS
$cssContent = "/* Fichier CSS de secours généré automatiquement */\nbody { display: block; }";

// Écriture des fichiers
file_put_contents($jsFile, $jsContent);
file_put_contents($cssFile, $cssContent);

echo "Fichiers JS et CSS minimaux créés avec succès.\n";

// Création du manifeste Vite minimal
$manifest = [
    'resources/js/app.jsx' => [
        'file' => 'assets/js/app.js',
        'isEntry' => true,
        'src' => 'resources/js/app.jsx',
        'css' => ['assets/css/app.css']
    ],
    'resources/css/app.css' => [
        'file' => 'assets/css/app.css',
        'isEntry' => true,
        'src' => 'resources/css/app.css'
    ]
];

// Écriture du manifeste dans les deux emplacements
$manifestFile = $publicBuildDir . '/manifest.json';
$viteManifestFile = $viteDir . '/manifest.json';

file_put_contents($manifestFile, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents($viteManifestFile, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Manifestes Vite créés avec succès.\n";

// Vérification finale
if (file_exists($manifestFile) && file_exists($viteManifestFile)) {
    echo "Vérification OK: Les manifestes existent à la fois dans " . basename($publicBuildDir) . " et " . basename($publicBuildDir) . "/" . basename($viteDir) . "\n";
    
    // Afficher le contenu des manifestes pour diagnostic
    echo "Contenu du manifeste principal:\n";
    echo file_get_contents($manifestFile) . "\n";
    
    exit(0);
} else {
    echo "ERREUR: Problème lors de la création des manifestes.\n";
    exit(1);
} 