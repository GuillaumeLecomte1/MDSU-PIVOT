<?php
// Script de diagnostic pour déterminer les problèmes avec Inertia.js

// Définir l'en-tête pour JSON
header('Content-Type: application/json');

// Informations sur l'environnement
$environment = [
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Inconnu',
    'document_root' => $_SERVER['DOCUMENT_ROOT'],
    'app_environment' => getenv('APP_ENV'),
    'app_debug' => (bool)getenv('APP_DEBUG'),
    'app_url' => getenv('APP_URL'),
    'asset_url' => getenv('ASSET_URL'),
];

// Vérification des fichiers clés
$files = [
    'app.js' => file_exists(__DIR__ . '/build/app.js'),
    'app.css' => file_exists(__DIR__ . '/build/app.css'),
    'manifest.json' => file_exists(__DIR__ . '/build/manifest.json'),
    'app.blade.php' => file_exists(__DIR__ . '/../resources/views/app.blade.php'),
];

// Vérifier si le répertoire des pages React existe
$reactPagesPath = __DIR__ . '/../resources/js/Pages';
$reactPages = [];
if (is_dir($reactPagesPath)) {
    $iterator = new DirectoryIterator($reactPagesPath);
    foreach ($iterator as $fileInfo) {
        if (!$fileInfo->isDot() && $fileInfo->isFile()) {
            $reactPages[] = $fileInfo->getFilename();
        }
    }
}

// Lire le contenu du manifeste Vite s'il existe
$manifest = [];
if (file_exists(__DIR__ . '/build/manifest.json')) {
    $manifest = json_decode(file_get_contents(__DIR__ . '/build/manifest.json'), true);
}

// Vérifier les composants Blade
$bladeComponents = [];
$componentPath = __DIR__ . '/../resources/views/components';
if (is_dir($componentPath)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($componentPath, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $fileInfo) {
        if ($fileInfo->isFile() && $fileInfo->getExtension() === 'php') {
            $relativePath = str_replace(__DIR__ . '/../resources/views/components/', '', $fileInfo->getPathname());
            $bladeComponents[] = $relativePath;
        }
    }
}

// Structure de la réponse
$response = [
    'timestamp' => time(),
    'environment' => $environment,
    'files' => $files,
    'react_pages' => $reactPages,
    'manifest' => $manifest,
    'blade_components' => $bladeComponents,
    'server_variables' => [
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'display_errors' => ini_get('display_errors'),
    ],
];

// Retourner les informations au format JSON
echo json_encode($response, JSON_PRETTY_PRINT); 