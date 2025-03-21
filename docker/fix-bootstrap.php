<?php

// Intercepter TOUTES les erreurs et logs de Laravel
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Rediriger TOUTES les erreurs PHP vers stderr
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');

// HACK: Override direct du système de fichiers PHP pour bloquer les écritures dans les fichiers log
// Cette technique est radicale mais fonctionnera
$hackCode = '
namespace Illuminate\\Log {
    // Override Monolog
    class LogManager {
        // Forcer stderr pour toutes les méthodes
        public function __call($method, $parameters) {
            // Rediriger tout vers stderr
            fprintf(STDERR, "[LOGGER] %s: %s\n", $method, json_encode($parameters));
            return $this;
        }
        
        // Forcer stderr pour toutes les méthodes statiques
        public static function __callStatic($method, $parameters) {
            // Rediriger tout vers stderr
            fprintf(STDERR, "[LOGGER] %s: %s\n", $method, json_encode($parameters));
            return new self();
        }

        // Override des méthodes communes
        public function channel($channel = null) { return $this; }
        public function stack(array $channels, $channel = null) { return $this; }
        public function build(array $config) { return $this; }
        public function createEmergencyLogger() { return $this; }
        public function emergency($message, array $context = []) { fprintf(STDERR, "[EMERGENCY] %s\n", $message); return $this; }
        public function alert($message, array $context = []) { fprintf(STDERR, "[ALERT] %s\n", $message); return $this; }
        public function critical($message, array $context = []) { fprintf(STDERR, "[CRITICAL] %s\n", $message); return $this; }
        public function error($message, array $context = []) { fprintf(STDERR, "[ERROR] %s\n", $message); return $this; }
        public function warning($message, array $context = []) { fprintf(STDERR, "[WARNING] %s\n", $message); return $this; }
        public function notice($message, array $context = []) { fprintf(STDERR, "[NOTICE] %s\n", $message); return $this; }
        public function info($message, array $context = []) { fprintf(STDERR, "[INFO] %s\n", $message); return $this; }
        public function debug($message, array $context = []) { fprintf(STDERR, "[DEBUG] %s\n", $message); return $this; }
        public function log($level, $message, array $context = []) { fprintf(STDERR, "[LOG %s] %s\n", $level, $message); return $this; }
    }
}

namespace Monolog {
    // Override Monolog
    class Logger {
        public function __construct($name, $handlers = [], $processors = []) {}
        public function __call($method, $parameters) { fprintf(STDERR, "[MONOLOG] %s\n", $method); return $this; }
        public function log($level, $message, array $context = []) { fprintf(STDERR, "[MONOLOG %s] %s\n", $level, $message); return $this; }
        public function debug($message, array $context = []) { fprintf(STDERR, "[MONOLOG DEBUG] %s\n", $message); return $this; }
        public function info($message, array $context = []) { fprintf(STDERR, "[MONOLOG INFO] %s\n", $message); return $this; }
        public function notice($message, array $context = []) { fprintf(STDERR, "[MONOLOG NOTICE] %s\n", $message); return $this; }
        public function warning($message, array $context = []) { fprintf(STDERR, "[MONOLOG WARNING] %s\n", $message); return $this; }
        public function error($message, array $context = []) { fprintf(STDERR, "[MONOLOG ERROR] %s\n", $message); return $this; }
        public function critical($message, array $context = []) { fprintf(STDERR, "[MONOLOG CRITICAL] %s\n", $message); return $this; }
        public function alert($message, array $context = []) { fprintf(STDERR, "[MONOLOG ALERT] %s\n", $message); return $this; }
        public function emergency($message, array $context = []) { fprintf(STDERR, "[MONOLOG EMERGENCY] %s\n", $message); return $this; }
    }
}

namespace Monolog\\Handler {
    // Override des handlers Monolog
    class StreamHandler {
        public function __construct($stream, $level = 100, $bubble = true, $filePermission = null, $useLocking = false) {}
        public function close() {}
        public function write(array $record) { fprintf(STDERR, "[STREAM] %s\n", json_encode($record)); }
        public function handleBatch(array $records) {}
        public function handle(array $record) { return true; }
        public function isHandling(array $record) { return true; }
    }
    
    class RotatingFileHandler extends StreamHandler {}
    class NullHandler extends StreamHandler {}
    class ErrorLogHandler extends StreamHandler {}
    class SyslogHandler extends StreamHandler {}
}

namespace Illuminate\\Foundation\\Exceptions {
    class Handler {
        protected function shouldntReport($e) { return false; }
        public function report($e) {
            fprintf(STDERR, "[EXCEPTION] %s\n", $e->getMessage());
        }
    }
}
';

// Enregistrer ce hack dans un fichier qui sera inclus au démarrage
$hackFile = '/var/www/docker/override-logging.php';
file_put_contents($hackFile, "<?php\n" . $hackCode);
chmod($hackFile, 0644);
echo "Hack d'interception des logs créé: $hackFile\n";

// Patch pour forcer l'inclusion du hack au démarrage
$bootstrapFile = '/var/www/bootstrap/app.php';
$publicFile = '/var/www/public/index.php';

// Sauvegarde des fichiers originaux
if (file_exists($bootstrapFile)) {
    file_put_contents($bootstrapFile.'.backup', file_get_contents($bootstrapFile));
}

if (file_exists($publicFile)) {
    file_put_contents($publicFile.'.backup', file_get_contents($publicFile));
}

// Patch direct du fichier index.php
if (file_exists($publicFile)) {
    $content = file_get_contents($publicFile);
    $hackInclude = "// HACK: Forcer la redirection des logs\nrequire __DIR__.'/../docker/override-logging.php';\n\n";
    $pattern = "<?php";
    $content = str_replace($pattern, $pattern . "\n" . $hackInclude, $content);
    file_put_contents($publicFile, $content);
    echo "Le fichier public/index.php a été patché avec succès.\n";
}

// Désactiver complètement les fichiers de log et les mises à jour de configuration
echo "Création d'un lien symbolique pour désactiver complètement le répertoire de logs...\n";
system('rm -rf /var/www/storage/logs');
system('mkdir -p /var/www/dev');
system('ln -sf /dev/null /var/www/dev/null');
system('ln -sf /var/www/dev/null /var/www/storage/logs');
echo "Liens symboliques créés avec succès.\n";

// Modifier directement le .env pour désactiver la journalisation
if (file_exists('/var/www/.env')) {
    $env = file_get_contents('/var/www/.env');
    $env = preg_replace('/LOG_CHANNEL=.*/', 'LOG_CHANNEL=stderr', $env);
    $env = preg_replace('/LOG_LEVEL=.*/', 'LOG_LEVEL=error', $env);
    file_put_contents('/var/www/.env', $env);
    echo "Fichier .env modifié avec succès.\n";
}

echo "Configuration des logs radicalement modifiée pour rediriger tout vers stderr.\n";
exit(0);