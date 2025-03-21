#!/bin/bash
set -e

echo "====== EXÉCUTION DES TÂCHES POST-DÉPLOIEMENT ======"

# S'assurer que tout est au bon propriétaire
chown -R www-data:www-data /var/www

# Vérifier que le dossier de logs est accessible
ls -la /var/www/storage/logs

# S'assurer que la configuration utilise stderr pour les logs
grep -r "LOG_CHANNEL" /var/www/.env || true
echo "Définition forcée de LOG_CHANNEL=stderr dans l'environnement"
export LOG_CHANNEL=stderr

# Vider tous les caches
cd /var/www
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Créer un fichier de test pour vérifier que les logs fonctionnent
php -r "file_put_contents('/var/www/storage/logs/test.log', 'Test post-deployment log file ' . date('Y-m-d H:i:s'));"
ls -la /var/www/storage/logs/test.log

echo "====== POST-DÉPLOIEMENT TERMINÉ ======" 