#!/bin/bash

# Script d'optimisation de Laravel pour la production
echo "Optimisation de Laravel pour la production..."

# Vérification de l'existence du fichier .env
if [ ! -f /var/www/.env ]; then
    echo "Fichier .env non trouvé, création à partir de .env.example"
    cp /var/www/.env.example /var/www/.env
    php /var/www/artisan key:generate
fi

cd /var/www

# Effacer les caches existants
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimiser composer
composer dump-autoload --optimize

# Générer les caches pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimisations supplémentaires
php artisan optimize

echo "Optimisation terminée!"

exit 0 