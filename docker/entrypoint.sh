#!/bin/bash
set -e

echo "====== DÉMARRAGE DU CONTENEUR ======"

# SOLUTION RADICALE: exécution en premier
echo "Application de la solution radicale pour les logs..."
/bin/bash /var/www/docker/radical-fix.sh

# SOLUTION AVEC INTERCEPTION PHP: modifie directement index.php
echo "Application du patch PHP d'interception des logs..."
php /var/www/docker/quick-fix.php

# Forcer l'utilisation de stderr pour les logs Laravel
export LOG_CHANNEL=null
export LOG_LEVEL=error

# Exécution du script de diagnostic des permissions
echo "Exécution du diagnostic des permissions..."
/bin/bash /var/www/docker/fix-permissions.sh

# Configuration des logs pour utiliser stdout au lieu de fichiers
echo "Configuration des logs Laravel pour utiliser STDOUT..."
cp -f /var/www/docker/logging.php /var/www/config/logging.php

# Patch direct du bootstrap de Laravel
echo "Application du patch pour le bootstrap de Laravel..."
php /var/www/docker/fix-bootstrap.php || true

# Vérification que le système est bien configuré
echo "Vérification finale de la configuration..."
php -v
cd /var/www && php artisan --version || true
cd /var/www && php artisan config:clear || true
cd /var/www && php artisan cache:clear || true

# Exécution du script post-déploiement en arrière-plan
echo "Exécution du script post-déploiement..."
nohup /bin/bash /var/www/docker/post-deploy.sh > /dev/null 2>&1 &

echo "====== DÉMARRAGE DE SUPERVISORD ======"

# Lancer supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf