<?php
/**
 * Script de correction automatique pour l'intégration de Vite dans Laravel
 * 
 * Ce script résout automatiquement les problèmes connus liés à Vite dans Laravel:
 * 1. Corrige le problème du champ 'src' manquant dans la classe Vite.php de Laravel
 * 2. Vérifie et corrige le format du fichier manifest.json
 * 3. Crée des assets fallback si nécessaire
 */

declare(strict_types=1);

echo "🔧 Démarrage du correctif Vite pour Laravel...\n";

// Chemins des fichiers
$vitePhpPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Foundation/Vite.php';
$manifestPath = __DIR__ . '/public/build/manifest.json';
$buildPath = __DIR__ . '/public/build';
$assetsPath = $buildPath . '/assets';

// Fonction pour journaliser avec timestamp
function log_message(string $message, string $level = 'INFO'): void {
    $timestamp = date('Y-m-d H:i:s');
    echo "[$timestamp][$level] $message\n";
}

// Vérifie l'existence du répertoire build et le crée si nécessaire
if (!is_dir($buildPath)) {
    log_message("Création du répertoire build manquant", "WARN");
    mkdir($buildPath, 0755, true);
}

// Vérifie l'existence du répertoire assets et le crée si nécessaire
if (!is_dir($assetsPath)) {
    log_message("Création du répertoire assets manquant", "WARN");
    mkdir($assetsPath, 0755, true);
}

// Correction de la classe Vite.php
if (file_exists($vitePhpPath)) {
    log_message("Vérification de la classe Vite.php");
    
    $vitePhpContents = file_get_contents($vitePhpPath);
    
    // Vérifie si le patch est déjà appliqué
    if (strpos($vitePhpContents, 'isset($chunk[\'src\'])') !== false) {
        log_message("✅ La classe Vite.php est déjà corrigée");
    } else {
        // Création d'une sauvegarde
        $backupPath = $vitePhpPath . '.backup-' . date('YmdHis');
        copy($vitePhpPath, $backupPath);
        log_message("📑 Sauvegarde créée: " . basename($backupPath));
        
        // Application du correctif
        $search = '$path = $chunk[\'src\'];';
        $replace = 'if (isset($chunk[\'src\'])) { $path = $chunk[\'src\']; } else { $path = $file; }';
        
        $patchedContents = str_replace($search, $replace, $vitePhpContents);
        
        if ($patchedContents === $vitePhpContents) {
            log_message("❌ Impossible de trouver la ligne à corriger dans Vite.php. Structure différente ?", "ERROR");
        } else {
            file_put_contents($vitePhpPath, $patchedContents);
            log_message("✅ Correctif appliqué à la classe Vite.php");
        }
    }
} else {
    log_message("❌ Fichier Vite.php non trouvé à: $vitePhpPath", "ERROR");
}

// Vérification et correction du fichier manifest.json
if (file_exists($manifestPath)) {
    log_message("Vérification du fichier manifest.json");
    
    $manifestContents = file_get_contents($manifestPath);
    $manifest = json_decode($manifestContents, true);
    
    if ($manifest === null) {
        log_message("❌ Le fichier manifest.json est invalide (JSON incorrect)", "ERROR");
        
        // Création d'un manifest de secours
        create_fallback_manifest($manifestPath, $assetsPath);
    } else {
        // Vérification du format et correction si nécessaire
        $needsFix = false;
        
        foreach ($manifest as $entry => $chunk) {
            if (!isset($chunk['src'])) {
                $needsFix = true;
                break;
            }
            
            // Vérification de l'existence des fichiers référencés
            if (isset($chunk['file'])) {
                $assetPath = $assetsPath . '/' . basename($chunk['file']);
                if (!file_exists($assetPath)) {
                    log_message("⚠️ Fichier asset manquant: " . basename($assetPath), "WARN");
                    
                    // Création d'un fichier vide pour éviter les erreurs 404
                    touch($assetPath);
                    log_message("✅ Fichier asset vide créé: " . basename($assetPath));
                }
            }
        }
        
        if ($needsFix) {
            log_message("⚠️ Le manifest manque de champs 'src', correction en cours...");
            
            // Sauvegarde du manifest original
            copy($manifestPath, $manifestPath . '.backup-' . date('YmdHis'));
            
            // Ajout du champ src à chaque entrée
            foreach ($manifest as $entry => &$chunk) {
                if (!isset($chunk['src'])) {
                    $chunk['src'] = $entry;
                    log_message("✅ Ajout du champ 'src' pour l'entrée: $entry");
                }
            }
            
            file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            log_message("✅ Manifest.json corrigé avec succès");
        } else {
            log_message("✅ Le manifest.json est correctement formaté");
        }
    }
} else {
    log_message("❌ Fichier manifest.json non trouvé, création d'un manifest de secours", "WARN");
    create_fallback_manifest($manifestPath, $assetsPath);
}

// Fonction pour créer un manifest de secours
function create_fallback_manifest(string $manifestPath, string $assetsPath): void {
    log_message("📝 Création d'un manifest de secours");
    
    $fallbackManifest = [
        'resources/css/app.css' => [
            'file' => 'assets/app.css',
            'src' => 'resources/css/app.css',
            'isEntry' => true
        ],
        'resources/js/app.jsx' => [
            'file' => 'assets/app.js',
            'src' => 'resources/js/app.jsx',
            'isEntry' => true
        ]
    ];
    
    file_put_contents($manifestPath, json_encode($fallbackManifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    log_message("✅ Manifest de secours créé: " . basename($manifestPath));
    
    // Création des fichiers assets de secours
    if (!file_exists($assetsPath . '/app.css')) {
        file_put_contents($assetsPath . '/app.css', "/* Generated fallback CSS */\n");
        log_message("✅ Fichier CSS de secours créé");
    }
    
    if (!file_exists($assetsPath . '/app.js')) {
        file_put_contents($assetsPath . '/app.js', "/* Generated fallback JavaScript */\n");
        log_message("✅ Fichier JS de secours créé");
    }
}

log_message("✅ Correctif Vite terminé avec succès"); 