<?php

// CONFIGURATION DE BASE
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');

// Logger de diagnostic simple sans dépendances
function pivot_log($message, $type = 'INDEX') {
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] [$type] $message");
}

// Capture d'erreurs fatales
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        pivot_log("FATAL ERROR: {$error['message']} in {$error['file']} on line {$error['line']}", 'FATAL');
        // Message d'erreur convivial
        if (!headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
            echo "<h1>Une erreur est survenue</h1>";
            echo "<p>Le serveur a rencontré une erreur interne. Veuillez réessayer plus tard.</p>";
        }
    }
});

pivot_log("Démarrage de l'application");

// Gestionnaire d'exceptions
set_exception_handler(function($e) {
    pivot_log("EXCEPTION: {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}", 'EXCEPTION');
});

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
    }
    @chmod($dir, 0777);
}

// Déterminer si l'application est en mode maintenance
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Chargement de l'application
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Traitement de la requête avec gestion des erreurs
try {
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    pivot_log("ERREUR DE TRAITEMENT: {$e->getMessage()}", 'ERROR');
    if (!headers_sent()) {
        header('HTTP/1.1 500 Internal Server Error');
        echo "<h1>Une erreur est survenue</h1>";
        echo "<p>Le serveur a rencontré une erreur interne. Veuillez réessayer plus tard.</p>";
    }
}
