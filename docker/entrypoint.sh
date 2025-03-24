#!/bin/bash
set -e

echo "====== INITIALISATION DE L'APPLICATION ======"

# Vérifier que l'application est au bon endroit
if [ ! -f "/var/www/artisan" ]; then
    echo "❌ ERREUR: Fichier artisan non trouvé. Le répertoire de travail est incorrect."
    exit 1
fi

# Vérifier la présence du fichier .env
if [ ! -f /var/www/.env ]; then
    echo "Création du fichier .env à partir de .env.example..."
    cp /var/www/.env.example /var/www/.env
    echo "✅ Fichier .env créé"
fi

# Générer une clé si nécessaire
if ! grep -q "^APP_KEY=" /var/www/.env || grep -q "^APP_KEY=$" /var/www/.env; then
    echo "Génération de la clé d'application..."
    php artisan key:generate --force
    echo "✅ Clé d'application générée"
fi

# Vérifier/corriger les permissions
echo "Configuration des permissions..."
chmod -R 755 /var/www/public
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
chown -R www-data:www-data /var/www
echo "✅ Permissions configurées"

# Créer le lien symbolique pour le stockage si nécessaire
if [ ! -L /var/www/public/storage ]; then
    echo "Création du lien symbolique pour le stockage..."
    php artisan storage:link
    echo "✅ Lien symbolique créé"
fi

# Créer/vérifier les répertoires d'assets
echo "Vérification des répertoires d'assets..."
mkdir -p /var/www/public/build/assets/js
mkdir -p /var/www/public/build/assets/css
mkdir -p /var/www/public/assets/js
mkdir -p /var/www/public/assets/css
echo "✅ Répertoires d'assets vérifiés"

# Exécuter le script de correction des assets
echo "Exécution du script de correction des assets..."
php /var/www/public/fix-assets.php > /var/www/storage/logs/fix-assets.log 2>&1
echo "✅ Script de correction exécuté (voir /var/www/storage/logs/fix-assets.log pour les détails)"

# Optimisations Laravel pour la production
echo "Application des optimisations Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Optimisations appliquées"

# Exécuter les migrations si la base de données est configurée
if grep -q "^DB_HOST=" /var/www/.env && grep -q "^DB_DATABASE=" /var/www/.env; then
    DB_HOST=$(grep "^DB_HOST=" /var/www/.env | cut -d'=' -f2)
    if [ -n "$DB_HOST" ]; then
        echo "Tentative de connexion à la base de données $DB_HOST..."
        # Attendre que la base de données soit prête (max 60 secondes)
        MAX_TRIES=12
        COUNTER=0
        
        until php -r "
        try {
            \$dbhost = '$DB_HOST';
            \$dbname = '$(grep "^DB_DATABASE=" /var/www/.env | cut -d'=' -f2)';
            \$dbuser = '$(grep "^DB_USERNAME=" /var/www/.env | cut -d'=' -f2)';
            \$dbpass = '$(grep "^DB_PASSWORD=" /var/www/.env | cut -d'=' -f2)';
            
            \$dsn = \"mysql:host=\$dbhost;dbname=\$dbname\";
            \$conn = new PDO(\$dsn, \$dbuser, \$dbpass);
            \$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo 'Connection successful!';
            exit(0);
        } catch(PDOException \$e) {
            echo 'Connection failed: ' . \$e->getMessage();
            exit(1);
        }
        " 2>/dev/null; do
            if [ $COUNTER -eq $MAX_TRIES ]; then
                echo "⚠️ Impossible de se connecter à la base de données après $MAX_TRIES tentatives."
                echo "⚠️ Les migrations ne seront pas exécutées."
                break
            fi
            echo "Tentative $((COUNTER+1))/$MAX_TRIES - Nouvelle tentative dans 5 secondes..."
            COUNTER=$((COUNTER+1))
            sleep 5
        done
        
        if [ $COUNTER -lt $MAX_TRIES ]; then
            echo "Exécution des migrations..."
            php artisan migrate --force
            echo "✅ Migrations exécutées"
        fi
    else
        echo "⚠️ Hôte de base de données non défini. Les migrations ne seront pas exécutées."
    fi
else
    echo "⚠️ Configuration de base de données incomplète. Les migrations ne seront pas exécutées."
fi

echo "====== DÉMARRAGE DU SERVEUR WEB ======"
# Exécuter la commande transmise (généralement apache2-foreground ou supervisord)
exec "$@"