#!/bin/sh
set -e

echo "🚀 Initialisation du conteneur..."

# Vérifier les répertoires et permissions
echo "📁 Vérification des répertoires et permissions..."
mkdir -p /var/www/storage/app/public
mkdir -p /var/www/storage/framework/{sessions,views,cache}
mkdir -p /var/www/storage/logs
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Créer le lien symbolique storage
if [ ! -L /var/www/public/storage ]; then
  echo "🔗 Création du lien symbolique storage..."
  php /var/www/artisan storage:link
fi

# Attendre la base de données si nécessaire
if [ -n "$DB_HOST" ]; then
  echo "⏳ Vérification de la connexion à la base de données externe..."
  counter=0
  until php -r "try { new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); echo 'Connection successful!'; } catch(PDOException \$e) { echo \$e->getMessage(); exit(1); }" > /dev/null 2>&1 || [ $counter -eq 10 ]; do
    counter=$((counter+1))
    echo "Tentative $counter/10: Connexion à la base de données..."
    sleep 5
  done
  
  if php -r "try { new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); echo 'OK'; } catch(PDOException \$e) { exit(1); }" > /dev/null 2>&1; then
    echo "✅ Base de données externe disponible, exécution des migrations..."
    php /var/www/artisan migrate --force
  else
    echo "⚠️ La base de données externe n'est pas accessible après plusieurs tentatives."
  fi
fi

# Optimisations Laravel pour la production
echo "⚙️ Optimisation de Laravel pour la production..."
php /var/www/artisan config:cache
php /var/www/artisan route:cache
php /var/www/artisan view:cache

# Démarrer Nginx et PHP-FPM
echo "✅ Démarrage des services..."
nginx
exec php-fpm