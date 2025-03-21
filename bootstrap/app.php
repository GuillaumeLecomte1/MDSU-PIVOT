<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// PATCH DE SÉCURITÉ POUR LES LOGS
// Désactivation des logs pour éviter les problèmes de permissions
putenv('LOG_CHANNEL=null');
putenv('LOG_LEVEL=emergency');

// Intercepte les erreurs au plus tôt
ini_set('log_errors', '1');
ini_set('error_log', 'php://stderr');

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })->create();
