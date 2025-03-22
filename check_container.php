<?php

echo "=== Diagnostic du conteneur Docker ===\n\n";

// Vérifier l'utilisateur courant
echo "Utilisateur courant: " . exec('whoami') . "\n";
echo "ID Utilisateur: " . posix_getuid() . "\n";
echo "ID Groupe: " . posix_getgid() . "\n";

// Informations système
echo "\n=== Informations système ===\n";
echo "Hostname: " . gethostname() . "\n";
echo "Système d'exploitation: " . php_uname() . "\n";
echo "Architecture: " . php_uname('m') . "\n";
echo "Uptime: " . exec('uptime 2>/dev/null') . "\n";

// Vérifier la mémoire disponible
echo "\n=== Mémoire système ===\n";
if (is_readable('/proc/meminfo')) {
    $meminfo = file_get_contents('/proc/meminfo');
    preg_match('/MemTotal:\s+(\d+)/', $meminfo, $matches);
    $totalMem = isset($matches[1]) ? round($matches[1]/1024) : 'Inconnu';
    
    preg_match('/MemFree:\s+(\d+)/', $meminfo, $matches);
    $freeMem = isset($matches[1]) ? round($matches[1]/1024) : 'Inconnu';
    
    preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $matches);
    $availableMem = isset($matches[1]) ? round($matches[1]/1024) : 'Inconnu';
    
    echo "Mémoire totale: $totalMem MB\n";
    echo "Mémoire libre: $freeMem MB\n";
    echo "Mémoire disponible: $availableMem MB\n";
} else {
    echo "Informations de mémoire non disponibles.\n";
}

// Vérifier l'espace disque
echo "\n=== Espace disque ===\n";
$df = exec('df -h / 2>/dev/null');
if ($df) {
    echo $df . "\n" . exec('df -h / | tail -1 2>/dev/null') . "\n";
} else {
    $totalSpace = disk_total_space('/');
    $freeSpace = disk_free_space('/');
    echo "Espace total: " . round($totalSpace / 1073741824, 2) . " GB\n";
    echo "Espace libre: " . round($freeSpace / 1073741824, 2) . " GB\n";
    echo "Espace utilisé: " . round(($totalSpace - $freeSpace) / 1073741824, 2) . " GB\n";
}

// Vérification des variables d'environnement
echo "\n=== Variables d'environnement ===\n";
$importantVars = [
    'PATH', 'HOSTNAME', 'HOME', 'USER', 'PWD',
    'APACHE_RUN_USER', 'APACHE_RUN_GROUP', 'APACHE_PID_FILE', 'APACHE_RUN_DIR', 'APACHE_LOCK_DIR', 'APACHE_LOG_DIR',
    'NGINX_VERSION', 'PHP_VERSION', 'PHP_INI_DIR', 'SUPERVISOR_VERSION',
    'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DB_CONNECTION',
    'APP_ENV', 'APP_DEBUG', 'APP_URL', 'APP_KEY'
];

foreach ($importantVars as $var) {
    $value = getenv($var);
    // Masquer les mots de passe
    if ($var === 'DB_PASSWORD' && $value) {
        $value = str_repeat('*', strlen($value));
    }
    echo "$var: " . ($value ?: 'Non défini') . "\n";
}

// Vérifier les processus en cours d'exécution
echo "\n=== Processus en cours d'exécution ===\n";
if (function_exists('exec')) {
    // Essayer d'utiliser ps
    $output = [];
    exec('ps aux 2>/dev/null', $output);
    if (!empty($output)) {
        echo implode("\n", $output) . "\n";
    } else {
        // Essayer d'utiliser top en mode batch
        exec('top -b -n 1 2>/dev/null', $output);
        if (!empty($output)) {
            echo implode("\n", $output) . "\n";
        } else {
            echo "Impossible d'obtenir la liste des processus.\n";
            
            // Vérifier les processus via /proc
            if (is_dir('/proc')) {
                echo "Processus actifs via /proc:\n";
                $processes = glob('/proc/[0-9]*');
                foreach ($processes as $proc) {
                    $pid = basename($proc);
                    if (is_readable("$proc/cmdline")) {
                        $cmdline = file_get_contents("$proc/cmdline");
                        $cmdline = str_replace("\0", " ", $cmdline);
                        echo "PID $pid: $cmdline\n";
                    }
                }
            }
        }
    }
} else {
    echo "La fonction exec() n'est pas disponible.\n";
}

// Vérifier les services
echo "\n=== Statut des services ===\n";
$services = ['nginx', 'php8.2-fpm', 'php-fpm', 'mysql', 'apache2', 'cron', 'supervisor', 'supervisord'];
foreach ($services as $service) {
    $status = exec("service $service status 2>/dev/null") ?: exec("systemctl status $service 2>/dev/null") ?: 'Inconnu';
    echo "$service: $status\n";
    
    // Vérifier spécifiquement si le service est actif
    $running = exec("pgrep -f $service 2>/dev/null");
    echo "  Processus trouvés: " . ($running ? 'Oui' : 'Non') . "\n";
}

// Vérifier les ports ouverts
echo "\n=== Ports ouverts ===\n";
if (function_exists('exec')) {
    $output = [];
    exec('netstat -tuln 2>/dev/null', $output);
    if (!empty($output)) {
        echo implode("\n", $output) . "\n";
    } else {
        exec('ss -tuln 2>/dev/null', $output);
        if (!empty($output)) {
            echo implode("\n", $output) . "\n";
        } else {
            echo "Impossible d'obtenir la liste des ports ouverts.\n";
        }
    }
} else {
    echo "La fonction exec() n'est pas disponible.\n";
}

// Vérifier la configuration de PHP
echo "\n=== Configuration PHP ===\n";
echo "Version PHP: " . PHP_VERSION . "\n";
echo "Extensions chargées: " . implode(', ', get_loaded_extensions()) . "\n";
echo "Fichier php.ini: " . php_ini_loaded_file() . "\n";
echo "Fichiers de configuration supplémentaires: " . implode(', ', php_ini_scanned_files() ?: ['Aucun']) . "\n";

echo "Paramètres importants:\n";
$phpSettings = [
    'display_errors', 'error_reporting', 'log_errors', 'error_log',
    'memory_limit', 'max_execution_time', 'upload_max_filesize', 'post_max_size',
    'opcache.enable', 'opcache.memory_consumption'
];

foreach ($phpSettings as $setting) {
    echo "  $setting: " . ini_get($setting) . "\n";
}

// Vérifier les fichiers de log
echo "\n=== Fichiers de log ===\n";
$logFiles = [
    '/var/log/nginx/error.log',
    '/var/log/nginx/access.log',
    '/var/log/apache2/error.log',
    '/var/log/apache2/access.log',
    '/var/log/php8.2-fpm.log',
    '/var/log/php-fpm.log',
    '/var/log/mysql/error.log',
    '/var/log/syslog',
    '/var/www/storage/logs/laravel.log',
    '/var/www/storage/logs/laravel-error.log'
];

foreach ($logFiles as $logFile) {
    if (file_exists($logFile)) {
        $size = filesize($logFile);
        echo "$logFile: " . round($size / 1024, 2) . " KB\n";
        
        if ($size > 0) {
            echo "Dernières lignes du fichier:\n";
            if (function_exists('exec')) {
                passthru("tail -5 $logFile 2>/dev/null");
                echo "\n";
            } else {
                $log = file($logFile);
                $lastLines = array_slice($log, max(0, count($log) - 5));
                echo implode('', $lastLines) . "\n";
            }
        } else {
            echo "Le fichier est vide.\n";
        }
    } else {
        echo "$logFile: N'existe pas\n";
    }
}

// Vérifier la configuration de Laravel
echo "\n=== Configuration Laravel ===\n";
if (is_readable('/var/www/.env')) {
    echo "Fichier .env trouvé.\n";
    echo "Taille: " . filesize('/var/www/.env') . " octets\n";
    echo "Permissions: " . substr(sprintf('%o', fileperms('/var/www/.env')), -4) . "\n";
} else {
    echo "Fichier .env non trouvé ou non lisible.\n";
}

// Vérifier les éléments spécifiques à Docker
echo "\n=== Informations Docker ===\n";
if (file_exists('/.dockerenv')) {
    echo "Ce script s'exécute dans un conteneur Docker.\n";
} else {
    echo "Ce script ne semble pas s'exécuter dans un conteneur Docker.\n";
}

if (is_readable('/proc/1/cgroup')) {
    $cgroup = file_get_contents('/proc/1/cgroup');
    echo "Informations cgroup:\n$cgroup\n";
}

echo "\n=== Fin du diagnostic conteneur ===\n"; 