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
echo "Création du lien symbolique pour le stockage..."
mkdir -p /var/www/storage/app/public
if [ ! -L /var/www/public/storage ]; then
    php artisan storage:link --force
    echo "✅ Lien symbolique créé"
else
    echo "✅ Lien symbolique existe déjà"
fi

# Vérifier les dossiers d'images importants
echo "Vérification des dossiers d'images..."
# Dossiers principaux
mkdir -p /var/www/storage/app/public/imagesAccueil
mkdir -p /var/www/storage/app/public/images/products
mkdir -p /var/www/storage/app/public/images/categories
mkdir -p /var/www/storage/app/public/images/users

# Sous-dossiers produits
mkdir -p /var/www/storage/app/public/images/products/thumbnails
mkdir -p /var/www/storage/app/public/images/products/large

# Sous-dossiers catégories
mkdir -p /var/www/storage/app/public/images/categories/icons
mkdir -p /var/www/storage/app/public/images/categories/banners

# Sous-dossiers utilisateurs
mkdir -p /var/www/storage/app/public/images/users/avatars
mkdir -p /var/www/storage/app/public/images/users/covers

# Autres dossiers d'images
mkdir -p /var/www/storage/app/public/images/banners
mkdir -p /var/www/storage/app/public/images/promotions
mkdir -p /var/www/storage/app/public/images/blog
mkdir -p /var/www/storage/app/public/images/blog/thumbnails
mkdir -p /var/www/storage/app/public/images/logos
mkdir -p /var/www/storage/app/public/images/icons
mkdir -p /var/www/storage/app/public/images/backgrounds

# Définir les permissions
chmod -R 777 /var/www/storage/app/public
echo "✅ Dossiers d'images configurés"

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