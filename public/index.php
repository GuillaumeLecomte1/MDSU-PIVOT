<?php

// =========== DÉSACTIVATION DES LOGS ===========
// Désactiver les logs au niveau PHP
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');

// Forcer la désactivation des logs Laravel
putenv('LOG_CHANNEL=null');
putenv('LOG_LEVEL=emergency');

// ===============================================

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// DIAGNOSTIC POUR ERREUR 502
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');

// Logger de diagnostic
function pivot_log($message, $type = 'INDEX') {
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] [$type] $message");
}

pivot_log("Démarrage du script index.php");

// Capturer les erreurs fatales
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        pivot_log("FATAL ERROR: {$error['message']} in {$error['file']} on line {$error['line']}", 'FATAL');
        
        // Afficher une page d'erreur conviviale
        if (!headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
            echo "<h1>Une erreur est survenue</h1>";
            echo "<p>Nous travaillons à résoudre ce problème. Veuillez réessayer ultérieurement.</p>";
            
            if (getenv('APP_DEBUG') == 'true') {
                echo "<h2>Détails techniques</h2>";
                echo "<p>{$error['message']} in {$error['file']} on line {$error['line']}</p>";
            }
        }
    }
});

// Gestionnaire d'exceptions personnalisé
set_exception_handler(function($e) {
    pivot_log("EXCEPTION: {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}", 'EXCEPTION');
    
    // Afficher une page d'erreur conviviale
    if (!headers_sent()) {
        header('HTTP/1.1 500 Internal Server Error');
        echo "<h1>Une exception est survenue</h1>";
        echo "<p>Nous travaillons à résoudre ce problème. Veuillez réessayer ultérieurement.</p>";
        
        if (getenv('APP_DEBUG') == 'true') {
            echo "<h2>Détails techniques</h2>";
            echo "<p>{$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}</p>";
            echo "<pre>{$e->getTraceAsString()}</pre>";
        }
    }
});

// Créer les répertoires nécessaires et définir les permissions
try {
    pivot_log("Vérification des répertoires critiques");
    
    $storageDirs = [
        __DIR__.'/../storage/logs',
        __DIR__.'/../storage/framework/sessions',
        __DIR__.'/../storage/framework/views',
        __DIR__.'/../storage/framework/cache',
        __DIR__.'/../bootstrap/cache',
    ];
    
    foreach ($storageDirs as $dir) {
        if (!is_dir($dir)) {
            pivot_log("Création du répertoire $dir");
            mkdir($dir, 0777, true);
        }
        chmod($dir, 0777);
    }
    
    if (!file_exists(__DIR__.'/../storage/logs/laravel.log')) {
        pivot_log("Création du fichier de log");
        touch(__DIR__.'/../storage/logs/laravel.log');
        chmod(__DIR__.'/../storage/logs/laravel.log', 0666);
    }
} catch (\Throwable $e) {
    pivot_log("ERREUR DE PRÉPARATION: {$e->getMessage()}", 'ERROR');
}

// Configure l'application pour utiliser stderr pour les logs
putenv('LOG_CHANNEL=stderr');

// Chargement de l'autoloader
pivot_log("Chargement de l'autoloader Composer");
try {
    require __DIR__.'/../vendor/autoload.php';
} catch (\Throwable $e) {
    pivot_log("ERREUR D'AUTOLOAD: {$e->getMessage()}", 'ERROR');
    throw $e;
}

pivot_log("Chargement du bootstrap de l'application");
try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
} catch (\Throwable $e) {
    pivot_log("ERREUR DE BOOTSTRAP: {$e->getMessage()}", 'ERROR');
    throw $e;
}

pivot_log("Création de la requête");
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::capture();
} catch (\Throwable $e) {
    pivot_log("ERREUR DE CRÉATION DE REQUÊTE: {$e->getMessage()}", 'ERROR');
    throw $e;
}

pivot_log("Traitement de la requête");
try {
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    pivot_log("ERREUR DE TRAITEMENT: {$e->getMessage()}", 'ERROR');
    throw $e;
}

pivot_log("Fin du script index.php");
