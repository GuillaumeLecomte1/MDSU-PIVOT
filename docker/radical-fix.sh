#!/bin/bash
set -e

echo "====== SOLUTION RADICALE POUR LES LOGS LARAVEL ======"

# Remplacer complètement le dossier des logs par /dev/null
echo "1. Suppression complète du répertoire de logs..."
rm -rf /var/www/storage/logs

# Créer un lien symbolique de /var/www/storage/logs vers /dev/null
echo "2. Création d'un lien symbolique vers /dev/null..."
ln -sf /dev/null /var/www/storage/logs

# Alternative: créer un lien symbolique vers un fichier en mémoire
echo "3. Au cas où le lien vers /dev/null ne fonctionne pas, création d'un répertoire de logs en mémoire..."
mkdir -p /dev/shm/laravel-logs
chmod 777 /dev/shm/laravel-logs
rm -rf /var/www/storage/logs
ln -sf /dev/shm/laravel-logs /var/www/storage/logs

# Ajouter une directive pour désactiver le logging dans .env
echo "4. Forçage de la configuration des logs dans .env..."
sed -i 's/LOG_CHANNEL=.*/LOG_CHANNEL=null/g' /var/www/.env 2>/dev/null || echo "LOG_CHANNEL=null" >> /var/www/.env

# Modification directe du fichier de configuration de Laravel
echo "5. Remplacement direct de la configuration des logs..."
mkdir -p /var/www/config
cat > /var/www/config/logging.php << 'EOL'
<?php
return [
    'default' => 'null',
    'channels' => [
        'null' => [
            'driver' => 'monolog',
            'handler' => Monolog\Handler\NullHandler::class,
        ],
        'stderr' => [
            'driver' => 'monolog',
            'handler' => Monolog\Handler\StreamHandler::class,
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],
        'single' => [
            'driver' => 'null',
        ],
        'daily' => [
            'driver' => 'null',
        ],
        'slack' => [
            'driver' => 'null',
        ],
        'papertrail' => [
            'driver' => 'null',
        ],
        'stack' => [
            'driver' => 'null',
        ],
        'emergency' => [
            'driver' => 'null',
        ],
    ],
];
EOL

# Créer un patch pour index.php
echo "6. Patching du fichier d'entrée pour intercepter tous les logs..."
INDEXFILE="/var/www/public/index.php"
if [ -f "$INDEXFILE" ]; then
  cp "$INDEXFILE" "$INDEXFILE.backup"
  cat > /tmp/index_patch.php << 'EOL'
<?php
// PATCH LOG INTERCEPTION
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');

// Désactiver les tentatives d'écriture de fichiers de log
class NullLogger {
    public function __call($method, $args) { return $this; }
    public static function __callStatic($method, $args) { return new self(); }
}
\Illuminate\Support\Facades\Log::swap(new NullLogger());
EOL

  cat /tmp/index_patch.php "$INDEXFILE.backup" > "$INDEXFILE"
fi

# Vérifier la configuration finale
echo "7. Vérification finale du système de logs..."
ls -la /var/www/storage/logs
ls -la /dev/shm/laravel-logs

echo "====== FIN DE LA SOLUTION RADICALE ======" 