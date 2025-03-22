<?php

// Afficher tous les détails de PHP pour le diagnostic
echo "=== Diagnostic PHP ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP Extensions:\n";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    echo "- $ext\n";
}

// Vérifier les permissions des dossiers importants
echo "\n=== Permissions des dossiers ===\n";
$paths = [
    '/var/www/storage',
    '/var/www/storage/framework',
    '/var/www/storage/framework/cache',
    '/var/www/storage/framework/sessions',
    '/var/www/storage/framework/views',
    '/var/www/storage/logs',
    '/var/www/bootstrap/cache'
];

foreach ($paths as $path) {
    if (file_exists($path)) {
        echo "$path: " . substr(sprintf('%o', fileperms($path)), -4) . " (Existe)\n";
        echo "  Writable: " . (is_writable($path) ? 'Oui' : 'Non') . "\n";
    } else {
        echo "$path: N'existe pas\n";
    }
}

// Vérifier la configuration
echo "\n=== Variables d'environnement ===\n";
$envVars = [
    'APP_ENV',
    'APP_DEBUG',
    'APP_URL',
    'DB_CONNECTION',
    'DB_HOST',
    'DB_PORT',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD', // Affichage masqué pour sécurité
    'CACHE_DRIVER',
    'QUEUE_CONNECTION',
    'SESSION_DRIVER',
    'LOG_CHANNEL',
];

foreach ($envVars as $var) {
    if ($var == 'DB_PASSWORD') {
        echo "$var: " . (empty(getenv($var)) ? 'Non défini' : '******') . "\n";
    } else {
        echo "$var: " . (getenv($var) ?: 'Non défini') . "\n";
    }
}

// Tester la connexion à la base de données
echo "\n=== Test de connexion à la base de données ===\n";

if (!extension_loaded('pdo_mysql')) {
    echo "L'extension PDO MySQL n'est pas chargée!\n";
} else {
    try {
        $dbHost = getenv('DB_HOST');
        $dbPort = getenv('DB_PORT');
        $dbName = getenv('DB_DATABASE');
        $dbUser = getenv('DB_USERNAME');
        $dbPass = getenv('DB_PASSWORD');

        if (empty($dbHost) || empty($dbName) || empty($dbUser)) {
            echo "Informations de connexion incomplètes.\n";
        } else {
            $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5,
            ];
            $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
            echo "Connexion à la base de données réussie.\n";
            
            // Vérifier les tables
            $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
            echo "Tables trouvées: " . count($tables) . "\n";
            if (count($tables) > 0) {
                echo "Premières tables: " . implode(', ', array_slice($tables, 0, 5)) . (count($tables) > 5 ? '...' : '') . "\n";
            }
        }
    } catch (Exception $e) {
        echo "Erreur de connexion à la base de données: " . $e->getMessage() . "\n";
    }
}

// Vérifier l'état du serveur web
echo "\n=== Serveur web ===\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Non défini') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Non défini') . "\n";

// Vérifier les processus système
echo "\n=== Processus PHP ===\n";
if (function_exists('exec')) {
    echo "Processus PHP-FPM:\n";
    exec('ps aux | grep php-fpm', $output);
    foreach ($output as $line) {
        echo "$line\n";
    }
    
    echo "\nProcessus Nginx:\n";
    exec('ps aux | grep nginx', $output2);
    foreach ($output2 as $line) {
        echo "$line\n";
    }
} else {
    echo "La fonction exec() n'est pas disponible.\n";
}

// Informations système
echo "\n=== Informations système ===\n";
echo "Heure du serveur: " . date('Y-m-d H:i:s') . "\n";
echo "Utilisation mémoire PHP: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
echo "Limite mémoire PHP: " . ini_get('memory_limit') . "\n";
if (function_exists('disk_free_space') && function_exists('disk_total_space')) {
    echo "Espace disque disponible: " . round(disk_free_space('/') / 1024 / 1024 / 1024, 2) . " GB\n";
    echo "Espace disque total: " . round(disk_total_space('/') / 1024 / 1024 / 1024, 2) . " GB\n";
}

// Fin du diagnostic
echo "\n=== Fin du diagnostic ===\n"; 