<?php

/**
 * Script pour corriger les URLs HTTP dans le manifeste Vite
 * Ce script vérifie le manifeste Vite et remplace toutes les URLs HTTP par des URLs HTTPS
 */

$manifestPath = '/var/www/public/build/manifest.json';

if (!file_exists($manifestPath)) {
    echo "Le fichier manifest.json n'existe pas à l'emplacement: $manifestPath\n";
    exit(1);
}

echo "Vérification du manifeste Vite pour les URLs HTTP...\n";

$manifest = json_decode(file_get_contents($manifestPath), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Erreur lors de la lecture du manifeste JSON: " . json_last_error_msg() . "\n";
    exit(1);
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
    file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
    echo "Manifeste mis à jour avec succès!\n";
} else {
    echo "Aucune URL HTTP trouvée dans le manifeste. Tout est déjà en HTTPS.\n";
}

echo "Vérification terminée.\n"; 