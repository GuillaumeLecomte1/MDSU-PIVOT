#!/bin/sh
set -e

echo "🚀 Démarrage de l'application Laravel..."

cd /var/www

# Vérifier si le fichier .env existe, sinon copier .env.example
if [ ! -f .env ]; then
    echo "📝 Création du fichier .env à partir de .env.example"
    cp .env.example .env
    
    # Générer une clé d'application si nécessaire
    echo "🔑 Génération de la clé d'application"
    php artisan key:generate --force
fi

# Attendre que la base de données MySQL soit disponible
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "⏳ Attente de la disponibilité de la base de données MySQL..."
    
    ATTEMPTS=0
    MAX_ATTEMPTS=30
    
    while [ $ATTEMPTS -lt $MAX_ATTEMPTS ]; do
        if mysql -h ${DB_HOST:-mysql} -u ${DB_USERNAME:-laravel} -p${DB_PASSWORD:-laravel} -e "SELECT 1" >/dev/null 2>&1; then
            echo "✅ Base de données MySQL disponible"
            break
        fi
        
        ATTEMPTS=$((ATTEMPTS + 1))
        echo "⏳ Tentative $ATTEMPTS/$MAX_ATTEMPTS : Attente de MySQL..."
        sleep 2
        
        if [ $ATTEMPTS -eq $MAX_ATTEMPTS ]; then
            echo "❌ Impossible de se connecter à MySQL après $MAX_ATTEMPTS tentatives"
            echo "⚠️ Continuation sans attendre MySQL..."
        fi
    done
fi

# Créer le lien symbolique pour le stockage
echo "🔗 Création du lien symbolique pour le stockage"
php artisan storage:link --force || true

# Migrations de base de données
echo "🗄️ Exécution des migrations de base de données"
php artisan migrate --force || echo "⚠️ Échec des migrations, continuation..."

# Optimisations
echo "⚡ Optimisation de l'application pour la production"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Définir les permissions
echo "🔒 Définition des permissions"
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;
chown -R www-data:www-data storage bootstrap/cache

# Démarrer supervisor pour gérer les processus
echo "🚦 Démarrage des services (nginx, php-fpm)"
exec supervisord -c /etc/supervisord.conf