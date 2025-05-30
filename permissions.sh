#!/bin/bash
# Modifier les permissions pour résoudre le problème d'accès au fichier de log

echo "Correction des permissions sur les répertoires de stockage..."

# Assurer que les répertoires existent
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/bootstrap/cache

# Créer le fichier de log s'il n'existe pas
touch /var/www/storage/logs/laravel.log

# Définir propriétaire sur www-data (utilisateur du serveur web)
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache

# Mettre les permissions 777 (lecture/écriture/exécution pour tous)
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache

echo "Permissions corrigées avec succès!"
