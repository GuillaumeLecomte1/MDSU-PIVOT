#!/bin/bash
set -e

# Paramètres par défaut pour l'environnement
export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}

echo "====== INITIALISATION DE L'APPLICATION ======"

# Copier le fichier d'environnement s'il n'existe pas
if [ ! -f .env ]; then
    echo "Création du fichier .env..."
    cp .env.example .env
    echo "✅ Fichier .env créé"
fi

# Générer la clé d'application si nécessaire
php artisan key:generate --force
echo "✅ Clé d'application générée"

# Optimisations pour la production
if [ "$APP_ENV" = "production" ]; then
    echo "Application des optimisations pour la production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    echo "✅ Optimisations appliquées"
fi

# Permissions
echo "Correction des permissions..."
chown -R www-data:www-data /var/www/
chmod -R 755 /var/www/storage
echo "✅ Permissions corrigées"

# Créer un lien symbolique pour le stockage si nécessaire
echo "Création du lien symbolique pour le stockage..."
php artisan storage:link
echo "✅ Lien symbolique créé"

# Préparation des assets
echo "Préparation des assets..."
mkdir -p /var/www/public/build /var/www/public/assets
php /var/www/public/fix-assets.php > /var/www/storage/logs/fix-assets.log
echo "✅ Assets préparés"

# Migrations de la base de données (uniquement si DB_HOST est défini)
if [ -n "$DB_HOST" ]; then
    echo "Attente de la connexion à la base de données..."
    # Attendre que la base de données soit prête
    sleep 5
    
    # Tentatives de connexion à la base de données
    MAX_TRIES=10
    TRIES=0
    until php artisan migrate:status > /dev/null 2>&1 || [ $TRIES -eq $MAX_TRIES ]; do
        echo "Tentative $((TRIES+1))/$MAX_TRIES de connexion à la base de données..."
        sleep 5
        TRIES=$((TRIES+1))
    done
    
    if [ $TRIES -eq $MAX_TRIES ]; then
        echo "⚠️ Impossible de se connecter à la base de données après $MAX_TRIES tentatives"
    else
        echo "Exécution des migrations..."
        php artisan migrate --force
        echo "✅ Migrations exécutées"
    fi
else
    echo "⚠️ Migrations ignorées (DB_HOST non défini)"
fi

echo "====== DÉMARRAGE DU SERVEUR WEB ======"

# Exécuter la commande transmise
exec "$@"