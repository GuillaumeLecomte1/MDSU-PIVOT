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
    php artisan key:generate --force || true
else
    echo "✅ Fichier .env trouvé"
fi

# Vérifier si les assets frontend sont présents, sinon utiliser les assets de secours
if [ ! -s public/build/assets/app.css ] || [ ! -s public/build/assets/app.js ]; then
    echo "⚠️ Assets frontend manquants, utilisation des assets de secours"
    mkdir -p public/build/assets
    cp -f fallback-assets/placeholder-css.css public/build/assets/app.css || true
    cp -f fallback-assets/placeholder-js.js public/build/assets/app.js || true
    echo '{"resources/css/app.css":{"file":"assets/app.css"},"resources/js/app.jsx":{"file":"assets/app.js"}}' > public/build/manifest.json
else
    echo "✅ Assets frontend trouvés"
fi

# Créer le lien symbolique pour le stockage s'il n'existe pas
if [ ! -L public/storage ]; then
    echo "🔗 Création du lien symbolique pour le stockage"
    php artisan storage:link --force || true
else
    echo "✅ Lien symbolique storage trouvé"
fi

# Attendre que la base de données MySQL soit disponible
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "⏳ Attente de la disponibilité de la base de données MySQL..."
    
    ATTEMPTS=0
    MAX_ATTEMPTS=10
    
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

# Migrations de base de données (avec gestion des erreurs)
echo "🗄️ Exécution des migrations de base de données"
php artisan migrate --force || echo "⚠️ Échec des migrations, continuation..."

# Optimisations avec gestion d'erreur
echo "⚡ Optimisation de l'application pour la production"
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan optimize || true

# Définir les permissions
echo "🔒 Définition des permissions"
find storage bootstrap/cache -type d -exec chmod 775 {} \; || true
find storage bootstrap/cache -type f -exec chmod 664 {} \; || true
chown -R www-data:www-data storage bootstrap/cache || true

# Démarrer supervisor pour gérer les processus
echo "🚦 Démarrage des services (nginx, php-fpm)"
exec supervisord -c /etc/supervisord.conf