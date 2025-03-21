<?php

// Chemins vers le fichier bootstrap/app.php
$bootstrapFile = '/var/www/bootstrap/app.php';

// Vérifier si le fichier existe
if (!file_exists($bootstrapFile)) {
    echo "Le fichier bootstrap/app.php n'existe pas.\n";
    exit(1);
}

// Lire le contenu du fichier
$content = file_get_contents($bootstrapFile);

// Chercher la fin du fichier
$returnPattern = "return \$app;";
$position = strrpos($content, $returnPattern);

if ($position === false) {
    echo "Impossible de trouver le pattern 'return \$app;' dans le fichier bootstrap/app.php.\n";
    exit(1);
}

// Créer une copie de sauvegarde du fichier original
file_put_contents($bootstrapFile.'.backup', $content);
echo "Une copie de sauvegarde a été créée: bootstrap/app.php.backup.\n";

// Ajouter la configuration des logs avant le return
$logConfigCode = <<<'EOD'

// PATCH DE SÉCURITÉ POUR LES LOGS
// Force stderr logging pour éviter les problèmes de permissions sur les fichiers
\Illuminate\Support\Facades\Log::extend('stderr', function ($app, array $config) {
    $handler = new \Monolog\Handler\StreamHandler('php://stderr');
    return new \Monolog\Logger('stderr', [$handler]);
});

// Force l'utilisation de stderr pour tous les canaux de logs
$app->singleton('log', function () {
    $handler = new \Monolog\Handler\StreamHandler('php://stderr');
    return new \Monolog\Logger('stderr', [$handler]);
});

// Set default log channel
$app->beforeBootstrapping(\Illuminate\Foundation\Bootstrap\ConfigureLogging::class, function($app) {
    // Force log channel to stderr
    config(['logging.default' => 'stderr']);
    // Replace all channel drivers with stderr
    foreach (config('logging.channels') as $channel => $config) {
        config(['logging.channels.'.$channel.'.driver' => 'stderr']);
    }
});

EOD;

// Insérer le code avant le return
$newContent = substr_replace($content, $logConfigCode, $position, 0);

// Écrire le nouveau contenu dans le fichier
if (file_put_contents($bootstrapFile, $newContent)) {
    echo "La configuration des logs a été ajoutée avec succès au fichier bootstrap/app.php.\n";
} else {
    echo "Erreur lors de l'écriture dans le fichier bootstrap/app.php.\n";
    exit(1);
}

// Si le fichier config/logging.php existe, le modifier aussi directement
$loggingFile = '/var/www/config/logging.php';
if (file_exists($loggingFile)) {
    // Créer une copie de sauvegarde
    file_put_contents($loggingFile.'.backup', file_get_contents($loggingFile));
    
    // Modifier directement la configuration avec une redirection brute
    $loggingContent = <<<'EOD'
<?php
return [
    'default' => 'stderr',
    'deprecations' => [
        'channel' => 'stderr',
        'trace' => false,
    ],
    'channels' => [
        'stack' => [
            'driver' => 'stderr',
        ],
        'single' => [
            'driver' => 'stderr',
        ],
        'daily' => [
            'driver' => 'stderr',
        ],
        'stderr' => [
            'driver' => 'stderr',
        ],
        'emergency' => [
            'driver' => 'stderr',
        ],
    ],
];
EOD;
    
    if (file_put_contents($loggingFile, $loggingContent)) {
        echo "La configuration des logs a été forcée dans le fichier config/logging.php.\n";
    } else {
        echo "Erreur lors de l'écriture dans le fichier config/logging.php.\n";
    }
}

exit(0); 