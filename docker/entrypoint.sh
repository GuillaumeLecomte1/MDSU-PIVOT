#!/bin/bash
set -e

echo "====== DÉMARRAGE DU CONTENEUR ======"

# Exécution du script de diagnostic des permissions
echo "Exécution du diagnostic des permissions..."
/bin/bash /var/www/docker/fix-permissions.sh

# Configuration des logs pour utiliser stdout au lieu de fichiers
echo "Configuration des logs Laravel pour utiliser STDOUT..."
cp -f /var/www/docker/logging.php /var/www/config/logging.php

# Vérification que le système est bien configuré
echo "Vérification finale de la configuration..."
php -v
cd /var/www && php artisan --version || true
cd /var/www && php artisan config:clear || true
cd /var/www && php artisan cache:clear || true

echo "====== DÉMARRAGE DE SUPERVISORD ======"

# Lancer supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf 