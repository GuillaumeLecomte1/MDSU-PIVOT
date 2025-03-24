<?php

// CONFIGURATION DE BASE
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');

// Logger de diagnostic simple sans dépendances
function pivot_log($message, $type = 'INDEX') {
    error_log("[$type] $message");
}

// Vérifier si on est en mode diagnostic
if (isset($_GET['diag'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'php_version' => phpversion(),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'server' => $_SERVER,
        'env' => getenv(),
    ], JSON_PRETTY_PRINT);
    exit;
}

// Enregistrer les erreurs fatales
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        pivot_log('ERREUR FATALE: ' . $error['message'] . ' dans ' . $error['file'] . ' ligne ' . $error['line'], 'FATAL');
    }
});

// Gestionnaire pour les requêtes d'assets manquantes
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isAssetRequest = preg_match('/\/(build\/assets|assets)\/(js|css)\/([a-zA-Z0-9_-]+)-([a-zA-Z0-9]+)\.(js|css)$/', $requestUri, $matches);

if ($isAssetRequest) {
    $basePath = $matches[1]; // build/assets ou assets
    $type = $matches[2];     // js ou css
    $name = $matches[3];     // nom du fichier (ex: app, vendor)
    $hash = $matches[4];     // hash (ex: CjAB3oxN)
    $ext = $matches[5];      // extension (js ou css)
    
    // Chemins alternatifs à essayer
    $possiblePaths = [
        __DIR__ . "/{$basePath}/{$type}/{$name}-{$hash}.{$ext}",
        __DIR__ . "/{$basePath}/{$type}/{$name}.{$ext}",
        __DIR__ . "/assets/{$type}/{$name}-{$hash}.{$ext}",
        __DIR__ . "/assets/{$type}/{$name}.{$ext}",
        __DIR__ . "/build/assets/{$type}/{$name}-{$hash}.{$ext}",
        __DIR__ . "/build/assets/{$type}/{$name}.{$ext}"
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            // Définir le type MIME
            $mime = ($ext === 'js') ? 'application/javascript' : 'text/css';
            header("Content-Type: {$mime}");
            header("X-Content-Source: asset-fallback");
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            readfile($path);
            exit;
        }
    }
    
    // Si aucun fichier n'est trouvé, créons-en un minimal
    $assetDir = __DIR__ . "/{$basePath}/{$type}";
    if (!is_dir($assetDir)) {
        mkdir($assetDir, 0755, true);
    }
    
    $targetFile = "{$assetDir}/{$name}-{$hash}.{$ext}";
    
    if ($ext === 'js') {
        $content = "console.log('Fallback {$name} JS bundle generated on-the-fly');";
    } else {
        $content = "/* Fallback {$name} CSS file generated on-the-fly */\nbody { font-family: sans-serif; }\n";
    }
    
    file_put_contents($targetFile, $content);
    
    // Servir le fichier créé
    $mime = ($ext === 'js') ? 'application/javascript' : 'text/css';
    header("Content-Type: {$mime}");
    header("X-Content-Source: generated-fallback");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    echo $content;
    exit;
}

try {
    pivot_log('Démarrage de l\'application');

    /*
    |--------------------------------------------------------------------------
    | Check If The Application Is Under Maintenance
    |--------------------------------------------------------------------------
    |
    | If the application is in maintenance / demo mode via the "down" command
    | we will load this file so that any pre-rendered content can be shown
    | instead of starting the framework, which could cause an exception.
    |
    */

    if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    /*
    |--------------------------------------------------------------------------
    | Register The Auto Loader
    |--------------------------------------------------------------------------
    |
    | Composer provides a convenient, automatically generated class loader for
    | this application. We just need to utilize it! We'll simply require it
    | into the script here so we don't need to manually load our classes.
    |
    */

    pivot_log('Chargement de l\'autoloader');
    require __DIR__.'/../vendor/autoload.php';

    /*
    |--------------------------------------------------------------------------
    | Run The Application
    |--------------------------------------------------------------------------
    |
    | Once we have the application, we can handle the incoming request using
    | the application's HTTP kernel. Then, we will send the response back
    | to this client's browser, allowing them to enjoy our application.
    |
    */

    pivot_log('Chargement du bootstrap');
    $app = require_once __DIR__.'/../bootstrap/app.php';

    pivot_log('Création du kernel');
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    pivot_log('Traitement de la requête');
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    pivot_log('Envoi de la réponse');
    $response->send();

    pivot_log('Terminaison de la requête');
    $kernel->terminate($request, $response);
} catch (Throwable $e) {
    pivot_log('EXCEPTION: ' . $e->getMessage() . ' dans ' . $e->getFile() . ' ligne ' . $e->getLine(), 'ERROR');
    
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    } else {
        echo "<h1>Erreur interne du serveur</h1>";
        echo "<p>Message: " . htmlspecialchars($e->getMessage()) . "</p>";
        
        if (getenv('APP_DEBUG') == 'true') {
            echo "<p>Fichier: " . htmlspecialchars($e->getFile()) . " à la ligne " . $e->getLine() . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
    }
    
    exit(1);
}
