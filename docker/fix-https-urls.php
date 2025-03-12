<?php

/**
 * Script pour corriger les URLs HTTP dans le manifeste Vite
 * Ce script vérifie le manifeste Vite et remplace toutes les URLs HTTP par des URLs HTTPS
 */

// Définir les chemins des manifestes
$manifestPath = '/var/www/public/build/manifest.json';
$viteManifestPath = '/var/www/public/build/.vite/manifest.json';

// Fonction pour vérifier et corriger un manifeste
function fixHttpUrls($path) {
    if (!file_exists($path)) {
        echo "Le fichier manifest.json n'existe pas à l'emplacement: $path\n";
        return false;
    }

    echo "Vérification du manifeste Vite pour les URLs HTTP à $path...\n";

    // Lire le contenu du fichier
    $content = file_get_contents($path);
    
    // Vérifier si le contenu est un JSON valide
    $manifest = json_decode($content, true);
    if ($manifest === null) {
        echo "Erreur lors de la lecture du manifeste JSON: " . json_last_error_msg() . "\n";
        return false;
    }

    // Remplacer directement les URLs HTTP par HTTPS dans le contenu
    $newContent = str_replace('http://', 'https://', $content);
    
    // Vérifier si des modifications ont été apportées
    if ($content !== $newContent) {
        echo "Des URLs HTTP ont été trouvées et remplacées par HTTPS.\n";
        file_put_contents($path, $newContent);
        echo "Manifeste mis à jour avec succès!\n";
        return true;
    } else {
        echo "Aucune URL HTTP trouvée dans le manifeste. Tout est déjà en HTTPS.\n";
        return true;
    }
}

// Vérifier et corriger le manifeste principal
echo "Traitement du manifeste principal...\n";
$mainResult = fixHttpUrls($manifestPath);

// Vérifier et corriger le manifeste dans .vite
echo "Traitement du manifeste .vite...\n";
$viteResult = fixHttpUrls($viteManifestPath);

// Synchroniser les manifestes si nécessaire
if ($mainResult && !$viteResult && file_exists($manifestPath)) {
    echo "Copie du manifeste principal vers le répertoire .vite...\n";
    if (!is_dir(dirname($viteManifestPath))) {
        mkdir(dirname($viteManifestPath), 0755, true);
    }
    copy($manifestPath, $viteManifestPath);
    echo "Manifeste copié avec succès.\n";
} elseif (!$mainResult && $viteResult && file_exists($viteManifestPath)) {
    echo "Copie du manifeste .vite vers le répertoire principal...\n";
    copy($viteManifestPath, $manifestPath);
    echo "Manifeste copié avec succès.\n";
}

echo "Vérification terminée.\n"; 