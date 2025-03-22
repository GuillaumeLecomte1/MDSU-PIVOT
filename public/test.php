<?php
// Fichier de test pour vérifier que PHP fonctionne
header('Content-Type: application/json');

echo json_encode([
    'status' => 'ok',
    'php_version' => PHP_VERSION,
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    'timestamp' => time(),
    'environment' => getenv('APP_ENV'),
    'laravel_path' => realpath(__DIR__ . '/../'),
    'storage_permissions' => is_writable(__DIR__ . '/../storage'),
    'cache_permissions' => is_writable(__DIR__ . '/../bootstrap/cache')
]); 