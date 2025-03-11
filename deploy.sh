#!/bin/bash

# Script de déploiement simplifié pour l'application Laravel

echo "🚀 Démarrage du déploiement..."

# Construire l'image Docker
echo "🔨 Construction de l'image Docker..."
docker build -t pivot-app:latest .

# Vérifier si la construction a réussi
if [ $? -ne 0 ]; then
  echo "❌ Échec de la construction de l'image Docker."
  exit 1
fi

echo "✅ Image Docker construite avec succès."

# Arrêter et supprimer le conteneur existant s'il existe
echo "🔄 Arrêt du conteneur existant..."
docker stop pivot-app 2>/dev/null || true
docker rm pivot-app 2>/dev/null || true

# Lancer le nouveau conteneur
echo "🚀 Lancement du nouveau conteneur..."
docker run -d --name pivot-app \
  -p 4004:4004 \
  -v /var/www/html/pivot/storage:/var/www/storage \
  -v /var/www/html/pivot/public/images:/var/www/public/images \
  -v /var/www/html/pivot/.env:/var/www/.env \
  --restart unless-stopped \
  pivot-app:latest

# Vérifier si le conteneur est en cours d'exécution
if [ "$(docker ps -q -f name=pivot-app)" ]; then
  echo "✅ Application déployée avec succès sur le port 4004!"
  echo "📊 Logs du conteneur:"
  docker logs pivot-app
else
  echo "❌ Échec du déploiement. Le conteneur n'est pas en cours d'exécution."
  echo "📊 Logs du conteneur:"
  docker logs pivot-app
  exit 1
fi 