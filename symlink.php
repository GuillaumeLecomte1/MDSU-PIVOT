<?php

/**
 * Script pour créer les liens symboliques nécessaires au fonctionnement de l'application
 * Utile en production quand l'accès SSH n'est pas disponible
 */

// Liens symboliques à créer
$links = [
    // Lien du dossier storage vers public/storage
    __DIR__ . '/storage/app/public' => __DIR__ . '/public/storage',
    
    // Si vous avez des dossiers de build spécifiques
    __DIR__ . '/public/build' => __DIR__ . '/public/assets',
];

// Créer chaque lien
foreach ($links as $target => $link) {
    // Vérifier si le dossier cible existe
    if (!file_exists($target)) {
        echo "⚠️ La cible n'existe pas: {$target}\n";
        continue;
    }
    
    // Supprimer le lien s'il existe déjà
    if (file_exists($link)) {
        if (is_link($link)) {
            unlink($link);
            echo "🗑️ Lien existant supprimé: {$link}\n";
        } else {
            echo "⚠️ {$link} existe mais n'est pas un lien symbolique. Suppression impossible.\n";
            continue;
        }
    }
    
    // Créer le lien
    if (symlink($target, $link)) {
        echo "✅ Lien créé: {$link} -> {$target}\n";
    } else {
        echo "❌ Erreur lors de la création du lien: {$link}\n";
    }
}

// Créer le lien storage si la fonction artisan est disponible
if (function_exists('exec')) {
    echo "🔄 Exécution de la commande Artisan pour le lien storage...\n";
    exec('php artisan storage:link', $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "✅ Commande artisan exécutée avec succès!\n";
        echo implode("\n", $output) . "\n";
    } else {
        echo "❌ Erreur lors de l'exécution de la commande artisan (code {$returnVar})\n";
        echo implode("\n", $output) . "\n";
    }
} else {
    echo "⚠️ La fonction exec() n'est pas disponible. Impossible d'exécuter la commande artisan.\n";
}

echo "✅ Opération terminée!\n"; 