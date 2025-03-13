<?php

/**
 * Script pour vérifier et corriger les problèmes liés à Pusher
 * Ce script vérifie si les variables d'environnement Pusher sont correctement définies
 * et modifie le fichier .env si nécessaire
 */

echo "Vérification des variables d'environnement Pusher...\n";

// Chemin vers le fichier .env
$envFile = '/var/www/.env';

// Vérifier si le fichier .env existe
if (!file_exists($envFile)) {
    echo "Le fichier .env n'existe pas à l'emplacement: $envFile\n";
    exit(1);
}

// Lire le contenu du fichier .env
$envContent = file_get_contents($envFile);

// Vérifier si BROADCAST_DRIVER est défini à pusher
if (preg_match('/BROADCAST_DRIVER\s*=\s*pusher/i', $envContent)) {
    echo "BROADCAST_DRIVER est défini à 'pusher'. Vérification des clés Pusher...\n";
    
    // Vérifier si les clés Pusher sont définies correctement
    if (preg_match('/PUSHER_APP_KEY\s*=\s*["\']?your_app_key["\']?/i', $envContent) ||
        preg_match('/PUSHER_APP_ID\s*=\s*["\']?your_app_id["\']?/i', $envContent) ||
        preg_match('/PUSHER_APP_SECRET\s*=\s*["\']?your_app_secret["\']?/i', $envContent)) {
        
        echo "Les clés Pusher ne sont pas correctement définies. Modification du BROADCAST_DRIVER à 'log'...\n";
        
        // Remplacer BROADCAST_DRIVER=pusher par BROADCAST_DRIVER=log
        $envContent = preg_replace('/BROADCAST_DRIVER\s*=\s*pusher/i', 'BROADCAST_DRIVER=log', $envContent);
        
        // Écrire le contenu modifié dans le fichier .env
        file_put_contents($envFile, $envContent);
        
        echo "BROADCAST_DRIVER modifié à 'log'.\n";
    } else {
        echo "Les clés Pusher semblent être correctement définies.\n";
    }
} else {
    echo "BROADCAST_DRIVER n'est pas défini à 'pusher'. Aucune modification nécessaire.\n";
}

echo "Vérification terminée.\n"; 