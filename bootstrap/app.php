<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// DIAGNOSTIC POUR ERREUR 502
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');

// Logger de diagnostic
function pivot_log($message, $type = 'BOOTSTRAP') {
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] [$type] $message");
}

pivot_log("Démarrage du bootstrap de l'application");

try {
    // Attempt to detect any fatal PHP errors
    register_shutdown_function(function () {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            pivot_log("FATAL ERROR: {$error['message']} in {$error['file']} on line {$error['line']}", 'FATAL');
        }
    });
    
    // Set exceptionally high memory limit for diagnosis
    ini_set('memory_limit', '512M');
    
    // Redirect Laravel's log to stderr
    if (!is_dir(__DIR__.'/../storage/logs')) {
        @mkdir(__DIR__.'/../storage/logs', 0777, true);
        pivot_log("Création du répertoire des logs");
    }
    @chmod(__DIR__.'/../storage/logs', 0777);
    pivot_log("Configuration des permissions du répertoire des logs");
    
    // Fix Bootstrap Error
    if (!class_exists(Illuminate\Foundation\Application::class)) {
        pivot_log("Classe Application non trouvée", "ERROR");
    } else {
        pivot_log("Classe Application trouvée");
    }
    
    try {
        pivot_log("Création de l'application Laravel");
        $app = Application::configure(basePath: dirname(__DIR__));
        
        pivot_log("Configuration du routage");
        $app = $app->withRouting(
            web: __DIR__.'/../routes/web.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
        );
        
        pivot_log("Configuration des middlewares");
        $app = $app->withMiddleware(function (Middleware $middleware) {
            //
        });
        
        pivot_log("Configuration des exceptions");
        $app = $app->withExceptions(function (Exceptions $exceptions) {
            //
        });
        
        pivot_log("Création et retour de l'application");
        return $app->create();
    } catch (\Throwable $inner) {
        pivot_log("ERREUR INTERNE: " . $inner->getMessage() . ' in ' . $inner->getFile() . ' on line ' . $inner->getLine(), "ERROR");
        throw $inner;
    }
} catch (\Throwable $e) {
    pivot_log("ERREUR BOOTSTRAP: " . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine(), "ERROR");
    
    // Tenter de retourner l'application par défaut en cas d'erreur
    pivot_log("Tentative de création d'une application de secours");
    return Application::configure(basePath: dirname(__DIR__))->create();
}
