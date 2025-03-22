<?php

echo "=== Test de lecture du fichier .env ===\n";
if (file_exists('/var/www/.env')) {
    echo "Le fichier .env existe\n";
    echo "Permissions: " . substr(sprintf('%o', fileperms('/var/www/.env')), -4) . "\n";
    echo "Taille: " . filesize('/var/www/.env') . " octets\n";
    echo "Propriétaire: " . posix_getpwuid(fileowner('/var/www/.env'))['name'] . "\n";
    echo "Groupe: " . posix_getgrgid(filegroup('/var/www/.env'))['name'] . "\n";
    echo "Lisible: " . (is_readable('/var/www/.env') ? 'Oui' : 'Non') . "\n";

    // Vérifier le contenu du .env
    echo "\n=== Les 5 premières lignes du fichier .env ===\n";
    $envContent = file_get_contents('/var/www/.env');
    $envLines = explode("\n", $envContent);
    $firstLines = array_slice($envLines, 0, 5);
    foreach ($firstLines as $line) {
        echo $line . "\n";
    }
    
    // Lire le contenu avec la fonction PHP parse_ini_file
    echo "\n=== Lecture avec parse_ini_file ===\n";
    try {
        $parsed = parse_ini_file('/var/www/.env');
        echo "Nombre de variables: " . count($parsed) . "\n";
        echo "Clés trouvées: " . implode(', ', array_keys($parsed)) . "\n";
    } catch (Exception $e) {
        echo "Erreur lors du parsing: " . $e->getMessage() . "\n";
    }
    
    // Vérifier comment Laravel lit le fichier .env
    echo "\n=== Test de lecture avec Dotenv ===\n";
    try {
        if (file_exists('/var/www/vendor/autoload.php')) {
            require_once '/var/www/vendor/autoload.php';
            
            if (class_exists('Dotenv\Dotenv')) {
                echo "La classe Dotenv existe.\n";
                try {
                    $dotenv = \Dotenv\Dotenv::createImmutable('/var/www');
                    $dotenv->load();
                    echo "Dotenv a chargé le fichier sans erreur.\n";
                    
                    echo "APP_NAME: " . ($_ENV['APP_NAME'] ?? 'Non défini') . "\n";
                    echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'Non défini') . "\n";
                    echo "APP_KEY: " . ($_ENV['APP_KEY'] ?? 'Non défini') . "\n";
                    echo "APP_DEBUG: " . ($_ENV['APP_DEBUG'] ?? 'Non défini') . "\n";
                    echo "APP_URL: " . ($_ENV['APP_URL'] ?? 'Non défini') . "\n";
                    echo "DB_CONNECTION: " . ($_ENV['DB_CONNECTION'] ?? 'Non défini') . "\n";
                    echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'Non défini') . "\n";
                    echo "DB_PORT: " . ($_ENV['DB_PORT'] ?? 'Non défini') . "\n";
                    echo "DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'Non défini') . "\n";
                    echo "DB_USERNAME: " . ($_ENV['DB_USERNAME'] ?? 'Non défini') . "\n";
                } catch (Exception $e) {
                    echo "Erreur lors du chargement avec Dotenv: " . $e->getMessage() . "\n";
                }
            } else {
                echo "La classe Dotenv n'existe pas.\n";
            }
        } else {
            echo "Le fichier vendor/autoload.php n'existe pas.\n";
        }
    } catch (Exception $e) {
        echo "Erreur: " . $e->getMessage() . "\n";
    }
} else {
    echo "Le fichier .env n'existe pas\n";
    
    // Rechercher les fichiers .env
    echo "\n=== Recherche de fichiers .env alternatifs ===\n";
    exec('find /var/www -name ".env*" 2>/dev/null', $envFiles);
    if (!empty($envFiles)) {
        echo "Fichiers trouvés:\n";
        foreach ($envFiles as $file) {
            echo "- $file (" . filesize($file) . " octets)\n";
        }
    } else {
        echo "Aucun fichier .env trouvé\n";
    }
}

echo "\n=== Fichiers dans le répertoire /var/www/ ===\n";
$files = scandir('/var/www/');
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "- $file\n";
    }
}

// Vérifier les variables d'environnement système
echo "\n=== Variables d'environnement système ===\n";
$relevantVars = ['APP_ENV', 'APP_DEBUG', 'APP_URL', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE'];
foreach ($relevantVars as $var) {
    echo "$var: " . (getenv($var) ?: 'Non défini') . "\n";
}

// Test de la connexion à la base de données sans utiliser les variables d'environnement
echo "\n=== Test direct de connexion à la base de données ===\n";
try {
    $pdo = new PDO('mysql:host=mysql;port=3306;dbname=pivot', 'root', '8ocxlumnakezr2wdfcwiijct2rejsgdr');
    echo "Connexion directe à la base de données réussie.\n";
    
    // Vérifier les tables
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables trouvées: " . count($tables) . "\n";
    if (count($tables) > 0) {
        echo "Premières tables: " . implode(', ', array_slice($tables, 0, 5)) . (count($tables) > 5 ? '...' : '') . "\n";
    }
} catch (PDOException $e) {
    echo "Erreur de connexion directe: " . $e->getMessage() . "\n";
} 