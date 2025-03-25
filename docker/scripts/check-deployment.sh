#!/bin/sh
# Script de vérification du déploiement Laravel/Inertia

echo "=== Vérification du déploiement Laravel/Inertia ==="

# Vérifiez si l'application est accessible
if curl -s http://localhost:${PORT:-4004} > /dev/null; then
  echo "✅ L'application est accessible sur le port ${PORT:-4004}"
else
  echo "❌ L'application n'est pas accessible sur le port ${PORT:-4004}"
fi

# Vérifiez si le lien symbolique storage existe
if [ -L /var/www/public/storage ]; then
  echo "✅ Le lien symbolique storage existe"
else
  echo "❌ Le lien symbolique storage n'existe pas"
fi

# Vérifiez si le manifest.json existe
if [ -f /var/www/public/build/manifest.json ]; then
  echo "✅ Le fichier manifest.json existe"
else
  echo "❌ Le fichier manifest.json n'existe pas"
fi

# Vérifiez les permissions des dossiers critiques
if [ "$(stat -c %U /var/www/storage)" = "www-data" ]; then
  echo "✅ Les permissions sur /var/www/storage sont correctes"
else
  echo "❌ Les permissions sur /var/www/storage sont incorrectes"
fi

if [ "$(stat -c %U /var/www/bootstrap/cache)" = "www-data" ]; then
  echo "✅ Les permissions sur /var/www/bootstrap/cache sont correctes"
else
  echo "❌ Les permissions sur /var/www/bootstrap/cache sont incorrectes"
fi

# Vérifiez si nginx est en cours d'exécution
if pgrep -x nginx > /dev/null; then
  echo "✅ Nginx est en cours d'exécution"
else
  echo "❌ Nginx n'est pas en cours d'exécution"
fi

# Vérifiez si php-fpm est en cours d'exécution
if pgrep -x php-fpm > /dev/null; then
  echo "✅ PHP-FPM est en cours d'exécution"
else
  echo "❌ PHP-FPM n'est pas en cours d'exécution"
fi

echo "=== Fin de la vérification ===" 