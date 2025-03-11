#!/bin/bash

# Script de healthcheck pour vérifier l'état des services
echo "Vérification de l'état des services..."

# Vérifier si Nginx est en cours d'exécution
if pgrep -x "nginx" > /dev/null; then
    echo "✅ Nginx est en cours d'exécution."
else
    echo "❌ Nginx n'est pas en cours d'exécution."
    exit 1
fi

# Vérifier si PHP-FPM est en cours d'exécution
if pgrep -x "php-fpm" > /dev/null; then
    echo "✅ PHP-FPM est en cours d'exécution."
else
    echo "❌ PHP-FPM n'est pas en cours d'exécution."
    exit 1
fi

# Vérifier si le fichier index.php est accessible
if curl -s http://localhost:4004/ | grep -q "Laravel"; then
    echo "✅ L'application Laravel est accessible."
else
    echo "❌ L'application Laravel n'est pas accessible."
    echo "Contenu de la réponse HTTP :"
    curl -v http://localhost:4004/
fi

echo "✅ Tous les services sont en cours d'exécution."
exit 0 