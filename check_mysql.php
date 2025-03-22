<?php

echo "=== Test de connexion MySQL ===\n\n";

// Informations de connexion directes
$host = 'mysql';
$port = 3306;
$dbname = 'pivot';
$username = 'root';
$password = '8ocxlumnakezr2wdfcwiijct2rejsgdr';

echo "Test de connexion à:\n";
echo "- Hôte: $host:$port\n";
echo "- Base de données: $dbname\n";
echo "- Utilisateur: $username\n";
echo "- Mot de passe: " . str_repeat('*', strlen($password)) . "\n\n";

// Test de disponibilité du serveur MySQL
echo "Test de disponibilité du serveur MySQL...\n";
$errno = $errstr = '';
$connection = @fsockopen($host, $port, $errno, $errstr, 5);
if (!$connection) {
    echo "Impossible de se connecter au serveur MySQL: $errstr ($errno)\n";
    echo "Vérification des hôtes disponibles...\n";
    
    // Liste des hôtes potentiels à tester
    $potentialHosts = ['mysql', 'localhost', '127.0.0.1', 'db', 'database', $host . '.pivot', 'pivot_mysql'];
    
    foreach ($potentialHosts as $testHost) {
        echo "Test de $testHost:$port... ";
        $testConnection = @fsockopen($testHost, $port, $errno, $errstr, 1);
        if ($testConnection) {
            echo "SUCCÈS\n";
            fclose($testConnection);
        } else {
            echo "ÉCHEC: $errstr\n";
        }
    }
} else {
    echo "Connexion TCP au serveur MySQL réussie.\n";
    fclose($connection);
}

// Test de connexion avec PDO
echo "\nTest de connexion avec PDO...\n";
try {
    $start = microtime(true);
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $time = round((microtime(true) - $start) * 1000, 2);
    echo "Connexion à la base de données réussie en $time ms.\n";

    // Tester l'accès aux tables
    echo "\n=== Tables de la base de données ===\n";
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    if (count($tables) > 0) {
        echo "Nombre de tables: " . count($tables) . "\n";
        echo "Liste des tables:\n";
        foreach ($tables as $index => $table) {
            echo ($index + 1) . ". $table\n";
            
            // Obtenir le nombre d'enregistrements
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "   - Nombre d'enregistrements: $count\n";
            
            // Vérifier la structure des tables importantes
            if (in_array($table, ['users', 'migrations', 'personal_access_tokens'])) {
                echo "   - Structure de la table:\n";
                $columns = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($columns as $column) {
                    echo "     * {$column['Field']} ({$column['Type']})" . 
                         ($column['Key'] == 'PRI' ? ' PRIMARY KEY' : '') . 
                         ($column['Null'] == 'NO' ? ' NOT NULL' : '') . "\n";
                }
            }
        }
    } else {
        echo "La base de données est vide (aucune table trouvée).\n";
    }
    
    // Vérification des procédures stockées et triggers
    echo "\n=== Routines et Triggers ===\n";
    $procedures = $pdo->query("SHOW PROCEDURE STATUS WHERE Db = '$dbname'")->fetchAll(PDO::FETCH_ASSOC);
    echo "Nombre de procédures stockées: " . count($procedures) . "\n";
    if (count($procedures) > 0) {
        foreach ($procedures as $proc) {
            echo "- {$proc['Name']} (créée: {$proc['Created']})\n";
        }
    }
    
    $triggers = $pdo->query("SHOW TRIGGERS")->fetchAll(PDO::FETCH_ASSOC);
    echo "Nombre de triggers: " . count($triggers) . "\n";
    if (count($triggers) > 0) {
        foreach ($triggers as $trigger) {
            echo "- {$trigger['Trigger']} ({$trigger['Timing']} {$trigger['Event']} sur {$trigger['Table']})\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Erreur de connexion PDO: " . $e->getMessage() . "\n";
    
    // Vérifier si le serveur MySQL est accessible mais la base n'existe pas
    try {
        $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
        echo "\nConnexion au serveur MySQL réussie, mais problème avec la base '$dbname'.\n";
        
        // Vérifier si la base existe
        $databases = $pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
        echo "Bases de données disponibles: " . implode(', ', $databases) . "\n";
        
        if (in_array($dbname, $databases)) {
            echo "La base '$dbname' existe mais est peut-être corrompue ou les privilèges sont insuffisants.\n";
        } else {
            echo "La base '$dbname' n'existe pas sur ce serveur.\n";
        }
    } catch (PDOException $e2) {
        echo "\nImpossible de se connecter au serveur MySQL: " . $e2->getMessage() . "\n";
    }
}

// Test de connexion avec mysqli
echo "\nTest de connexion avec mysqli...\n";
try {
    $mysqli = new mysqli($host, $username, $password, $dbname, $port);
    if ($mysqli->connect_errno) {
        echo "Erreur de connexion mysqli: " . $mysqli->connect_error . "\n";
    } else {
        echo "Connexion mysqli réussie.\n";
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "Exception mysqli: " . $e->getMessage() . "\n";
}

// Vérification des logs MySQL si disponibles
echo "\n=== Logs d'erreur MySQL ===\n";
echo "Note: Cette vérification ne fonctionnera que si le conteneur a accès aux logs MySQL\n";

// Essayer plusieurs emplacements de logs possibles
$potentialLogLocations = [
    '/var/log/mysql/error.log',
    '/var/log/mysql.err',
    '/var/log/mysql.log',
    '/var/log/mysqld.log'
];

$logFound = false;
foreach ($potentialLogLocations as $logFile) {
    if (file_exists($logFile) && is_readable($logFile)) {
        echo "Log trouvé: $logFile\n";
        $logFound = true;
        echo "Dernières erreurs (10 lignes):\n";
        system("tail -10 $logFile");
        break;
    }
}

if (!$logFound) {
    echo "Aucun fichier de log MySQL accessible.\n";
}

echo "\n=== Configuration PHP pour MySQL ===\n";
echo "Extensions PHP chargées: " . implode(', ', get_loaded_extensions()) . "\n";
echo "pdo_mysql chargé: " . (extension_loaded('pdo_mysql') ? 'Oui' : 'Non') . "\n";
echo "mysqli chargé: " . (extension_loaded('mysqli') ? 'Oui' : 'Non') . "\n";

if (function_exists('ini_get')) {
    echo "mysql.default_socket: " . ini_get('mysql.default_socket') . "\n";
    echo "mysqli.default_socket: " . ini_get('mysqli.default_socket') . "\n";
    echo "pdo_mysql.default_socket: " . ini_get('pdo_mysql.default_socket') . "\n";
}

echo "\n=== Fin des tests MySQL ===\n"; 