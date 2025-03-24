#!/bin/bash
set -e

echo "====== INITIALISATION DE L'APPLICATION ======"

# Vérifier que le répertoire de travail est correct
if [ ! -f "/var/www/artisan" ]; then
    echo "❌ ERROR: artisan file not found. Working directory is incorrect."
    exit 1
fi

# Répertoires de logs
mkdir -p /tmp/laravel-logs
chmod -R 777 /tmp/laravel-logs
chown -R www-data:www-data /tmp/laravel-logs

# Permissions
echo "Configuration des permissions..."
chmod -R 755 /var/www/public
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache
chown -R www-data:www-data /var/www
echo "✅ Permissions configurées"

# Générer la clé si nécessaire
php artisan key:generate --force
echo "✅ Clé d'application générée"

# Lien symbolique pour le stockage
php artisan storage:link --force
echo "✅ Lien symbolique créé"

# Fix les assets
echo "Création des assets de fallback..."
php /var/www/public/fix-assets.php > /tmp/laravel-logs/fix-assets.log 2>&1
echo "✅ Assets de fallback créés"

# Optimisations Laravel
echo "Application des optimisations Laravel..."
php artisan optimize
php artisan view:cache
echo "✅ Optimisations appliquées"

# Exécuter les migrations si la base de données est configurée
if grep -q "^DB_HOST=" /var/www/.env; then
    DB_HOST=$(grep "^DB_HOST=" /var/www/.env | cut -d'=' -f2)
    if [ -n "$DB_HOST" ]; then
        echo "Attente de la base de données..."
        php artisan migrate --force
        echo "✅ Migrations exécutées"
    fi
fi

echo "====== DÉMARRAGE DES SERVICES ======"
exec "$@"