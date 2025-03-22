<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Configuration de base
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');

// Logger simple - vérifier si la fonction n'existe pas déjà avant de la déclarer
if (!function_exists('bootstrap_log')) {
    function bootstrap_log($message) {
        error_log("[BOOTSTRAP] $message");
    }
}

bootstrap_log("Démarrage de l'application");

// Vérification des répertoires essentiels
$storageDirs = [
    __DIR__.'/../storage/logs',
    __DIR__.'/../storage/framework/sessions',
    __DIR__.'/../storage/framework/views',
    __DIR__.'/../storage/framework/cache',
    __DIR__.'/../bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
        bootstrap_log("Création du répertoire: $dir");
    }
    @chmod($dir, 0777);
}

// Augmenter la limite de mémoire
ini_set('memory_limit', '256M');

try {
    bootstrap_log("Création de l'instance Application");
    
    // Création de l'application Laravel avec configuration standard
    return Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__.'/../routes/web.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware) {
            // Vérifier si le middleware existe avant de l'ajouter
            $webMiddleware = [];
            
            if (class_exists(\App\Http\Middleware\HandleInertiaRequests::class)) {
                $webMiddleware[] = \App\Http\Middleware\HandleInertiaRequests::class;
            }
            
            if (class_exists(\Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class)) {
                $webMiddleware[] = \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class;
            }
            
            $middleware->web(append: $webMiddleware);
        })
        ->withExceptions(function (Exceptions $exceptions) {
            // Configuration par défaut des exceptions
        })
        ->create();
} catch (\Throwable $e) {
    bootstrap_log("Erreur: " . $e->getMessage());
    
    // En cas d'erreur, tenter de créer une application minimale
    try {
        return Application::configure(basePath: dirname(__DIR__))->create();
    } catch (\Throwable $e2) {
        error_log("ERREUR FATALE: " . $e2->getMessage());
        exit(1);
    }
}
