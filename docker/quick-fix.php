<?php

/**
 * EMERGENCY QUICK-FIX POUR LES LOGS LARAVEL
 * 
 * Ce script modifie directement la façon dont Laravel initialise les logs
 * en remplaçant des classes et des méthodes clés du framework.
 */

// Fichier à modifier (point d'entrée principal)
$indexFile = '/var/www/public/index.php';

if (!file_exists($indexFile)) {
    echo "ERREUR: Le fichier index.php n'existe pas à l'emplacement attendu.\n";
    exit(1);
}

// Contenu du patch à insérer au début du fichier
$patchCode = <<<'EOF'
<?php
// =========== EMERGENCY LOG FIX ===========
// Ce patch force la désactivation complète des logs fichiers

// 1. Rediriger toutes les erreurs PHP vers stderr
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');

// 2. Désactiver les logs dans le .env à la volée
putenv('LOG_CHANNEL=null');

// 3. Mock de classes critiques pour les logs
class LogNullifier {
    public function __call($method, $args) { return $this; }
    public static function __callStatic($method, $args) { return new self(); }
}

// 4. Préparation de l'interception du Log Facade
spl_autoload_register(function ($class) {
    if ($class === 'Illuminate\\Support\\Facades\\Log') {
        eval('namespace Illuminate\\Support\\Facades; class Log { 
            public static function __callStatic($method, $args) { 
                return new \\LogNullifier(); 
            }
            public static function channel() { return new \\LogNullifier(); }
            public static function stack() { return new \\LogNullifier(); }
            public static function build() { return new \\LogNullifier(); }
        }');
        return true;
    }
    
    if ($class === 'Monolog\\Handler\\StreamHandler') {
        eval('namespace Monolog\\Handler; class StreamHandler {
            public function __construct() {}
            public function handle() { return true; }
            public function handleBatch() {}
            public function isHandling() { return true; }
            public function write() {}
        }');
        return true;
    }
    
    return false;
}, true, true);

// 5. S'assurer que le dossier de logs n'existe pas pour éviter les tentatives d'écriture
@mkdir('/dev/shm/fake-logs', 0777, true);
@symlink('/dev/shm/fake-logs', '/var/www/storage/logs');

// ============= END OF FIX ===============

EOF;

// Sauvegarder le fichier original
copy($indexFile, $indexFile . '.original');
echo "Sauvegarde du fichier original créée: {$indexFile}.original\n";

// Lire le contenu actuel
$currentContent = file_get_contents($indexFile);

// Extraire la partie après <?php 
$phpPos = strpos($currentContent, '<?php');
if ($phpPos !== false) {
    $contentAfterPhp = substr($currentContent, $phpPos + 5);
    $newContent = $patchCode . $contentAfterPhp;
    
    // Écrire le nouveau contenu
    if (file_put_contents($indexFile, $newContent)) {
        echo "PATCH APPLIQUÉ AVEC SUCCÈS! Le fichier index.php a été modifié.\n";
    } else {
        echo "ERREUR: Impossible d'écrire dans le fichier index.php.\n";
        exit(1);
    }
} else {
    echo "ERREUR: Impossible de trouver la balise PHP d'ouverture dans index.php.\n";
    exit(1);
}

// Désactiver tous les canaux de logs dans la configuration
$loggingConfig = '/var/www/config/logging.php';
if (file_exists($loggingConfig)) {
    $nullConfig = <<<'EOD'
<?php
return [
    'default' => 'null',
    'channels' => [
        'stack' => ['driver' => 'null'],
        'single' => ['driver' => 'null'],
        'daily' => ['driver' => 'null'],
        'slack' => ['driver' => 'null'],
        'stderr' => ['driver' => 'null'],
        'syslog' => ['driver' => 'null'],
        'errorlog' => ['driver' => 'null'],
        'null' => ['driver' => 'null'],
        'emergency' => ['driver' => 'null'],
    ],
];
EOD;
    file_put_contents($loggingConfig, $nullConfig);
    echo "Configuration des logs complètement désactivée.\n";
}

echo "OPÉRATION TERMINÉE: Tous les logs Laravel sont maintenant désactivés.\n";
exit(0); 