<?php
/**
 * Script d'urgence pour corriger les problèmes d'assets en production.
 * À exécuter directement via HTTP: https://pivot.guillaume-lcte.fr/fix-assets.php
 */

header('Content-Type: text/plain');
echo "=== Script de correction des assets ===\n\n";

// Créer le répertoire build s'il n'existe pas
$buildDir = __DIR__ . '/build';
if (!is_dir($buildDir)) {
    echo "Création du répertoire build...\n";
    mkdir($buildDir, 0755, true);
    echo "✅ Répertoire build créé.\n";
} else {
    echo "Le répertoire build existe déjà.\n";
}

// Créer le répertoire assets s'il n'existe pas
$assetsDir = __DIR__ . '/assets';
if (!is_dir($assetsDir)) {
    echo "Création du répertoire assets...\n";
    mkdir($assetsDir, 0755, true);
    echo "✅ Répertoire assets créé.\n";
} else {
    echo "Le répertoire assets existe déjà.\n";
}

// Créer des sous-répertoires pour les assets
$subDirs = ['js', 'css', 'images', 'fonts'];
foreach ($subDirs as $dir) {
    if (!is_dir($assetsDir . '/' . $dir)) {
        echo "Création du sous-répertoire {$dir}...\n";
        mkdir($assetsDir . '/' . $dir, 0755, true);
        echo "✅ Sous-répertoire {$dir} créé.\n";
    } else {
        echo "Le sous-répertoire {$dir} existe déjà.\n";
    }

    // Créer également les sous-répertoires dans build
    if (!is_dir($buildDir . '/assets/' . $dir)) {
        echo "Création du sous-répertoire build/assets/{$dir}...\n";
        mkdir($buildDir . '/assets/' . $dir, 0755, true);
        echo "✅ Sous-répertoire build/assets/{$dir} créé.\n";
    }
}

// Créer un fichier .htaccess dans assets pour le fallback vers build
$htaccessContent = <<<EOT
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to build directory if file not found
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /build/assets/\$1 [L,QSA]
</IfModule>
EOT;

$htaccessFile = $assetsDir . '/.htaccess';
if (!file_exists($htaccessFile)) {
    echo "Création du fichier .htaccess dans assets...\n";
    file_put_contents($htaccessFile, $htaccessContent);
    echo "✅ Fichier .htaccess créé dans assets.\n";
} else {
    echo "Le fichier .htaccess existe déjà dans assets.\n";
}

// Créer un fichier .htaccess dans build pour le fallback vers assets
$buildHtaccessContent = <<<EOT
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to assets directory if file not found
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /assets/\$1 [L,QSA]
</IfModule>
EOT;

$buildHtaccessFile = $buildDir . '/.htaccess';
if (!file_exists($buildHtaccessFile)) {
    echo "Création du fichier .htaccess dans build...\n";
    file_put_contents($buildHtaccessFile, $buildHtaccessContent);
    echo "✅ Fichier .htaccess créé dans build.\n";
} else {
    echo "Le fichier .htaccess existe déjà dans build.\n";
}

// Créer des fichiers JavaScript et CSS minimaux si nécessaires
$jsFilePath = $assetsDir . '/js/app.js';
if (!file_exists($jsFilePath)) {
    echo "Création d'un fichier JavaScript minimal...\n";
    $jsContent = "console.log('Application Pivot - Fichier généré par fix-assets.php');";
    file_put_contents($jsFilePath, $jsContent);
    echo "✅ Fichier JavaScript minimal créé.\n";
}

$cssFilePath = $assetsDir . '/css/app.css';
if (!file_exists($cssFilePath)) {
    echo "Création d'un fichier CSS minimal...\n";
    $cssContent = "/* Fichier généré par fix-assets.php */\n";
    file_put_contents($cssFilePath, $cssContent);
    echo "✅ Fichier CSS minimal créé.\n";
}

// Créer un fichier manifest.json minimal
$manifestContent = <<<EOT
{
    "resources/js/app.jsx": {
        "file": "js/app.js",
        "isEntry": true,
        "src": "resources/js/app.jsx"
    },
    "resources/css/app.css": {
        "file": "css/app.css",
        "isEntry": true,
        "src": "resources/css/app.css"
    }
}
EOT;

$manifestFile = $assetsDir . '/manifest.json';
if (!file_exists($manifestFile)) {
    echo "Création d'un fichier manifest.json minimal...\n";
    file_put_contents($manifestFile, $manifestContent);
    echo "✅ Fichier manifest.json créé.\n";
} else {
    echo "Le fichier manifest.json existe déjà.\n";
}

// Copier le manifest dans le répertoire build
$buildManifestFile = $buildDir . '/manifest.json';
if (!file_exists($buildManifestFile)) {
    echo "Copie du manifest.json vers build...\n";
    copy($manifestFile, $buildManifestFile);
    echo "✅ Fichier manifest.json copié vers build.\n";
}

echo "\n✅ Opération terminée. Rechargez la page principale et vérifiez si les assets se chargent correctement."; 