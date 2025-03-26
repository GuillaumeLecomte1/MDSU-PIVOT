#!/bin/sh
set -e

cd /var/www/html

# Attente de la disponibilité de la base de données
echo "Attente de la base de données..."
php -r "
\$attempts = 0;
\$max_attempts = 60;
\$connected = false;

while (!\$connected && \$attempts < \$max_attempts) {
    try {
        \$dbh = new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        \$connected = true;
        echo \"Connexion à la base de données réussie.\n\";
    } catch (PDOException \$e) {
        \$attempts++;
        echo \"Tentative \$attempts/\$max_attempts: Impossible de se connecter à la base de données. Nouvelle tentative dans 1 seconde...\n\";
        sleep(1);
    }
}

if (!\$connected) {
    echo \"Impossible de se connecter à la base de données après \$max_attempts tentatives. Abandon.\n\";
    exit(1);
}
"

# Vérification et génération de la clé d'application si nécessaire
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" ]; then
    echo "Génération d'une nouvelle clé d'application..."
    php artisan key:generate
fi

# Migrations et cache
echo "Exécution des migrations..."
php artisan migrate --force

echo "Mise en cache de la configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Démarrage de supervisor
echo "Démarrage des services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf 