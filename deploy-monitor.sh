#!/bin/bash

# Script pour surveiller le déploiement Docker et éviter les timeouts

echo "🚀 Démarrage du déploiement avec surveillance..."

# Lancer le build Docker en arrière-plan
docker build -t pivot-app:latest . &
BUILD_PID=$!

# Fonction pour afficher l'état du build
function show_build_status() {
  echo "⏳ Build en cours... (PID: $BUILD_PID)"
  docker ps -a
  echo "Dernières lignes des logs Docker:"
  docker logs $(docker ps -q -n 1) 2>&1 | tail -n 10
}

# Surveiller le build avec un timeout de 30 minutes
TIMEOUT=1800  # 30 minutes en secondes
START_TIME=$(date +%s)

while kill -0 $BUILD_PID 2>/dev/null; do
  CURRENT_TIME=$(date +%s)
  ELAPSED_TIME=$((CURRENT_TIME - START_TIME))
  
  if [ $ELAPSED_TIME -gt $TIMEOUT ]; then
    echo "❌ Timeout dépassé (30 minutes). Le build prend trop de temps."
    kill -9 $BUILD_PID
    exit 1
  fi
  
  # Afficher l'état toutes les 30 secondes
  if [ $((ELAPSED_TIME % 30)) -eq 0 ]; then
    show_build_status
  fi
  
  sleep 5
done

# Vérifier si le build s'est terminé avec succès
wait $BUILD_PID
BUILD_STATUS=$?

if [ $BUILD_STATUS -eq 0 ]; then
  echo "✅ Build terminé avec succès!"
  
  # Arrêter le conteneur existant s'il existe
  docker stop pivot-app 2>/dev/null || true
  docker rm pivot-app 2>/dev/null || true
  
  # Lancer le nouveau conteneur
  docker run -d --name pivot-app -p 4004:4004 \
    -v /var/www/html/pivot/storage:/var/www/storage \
    -v /var/www/html/pivot/public/images:/var/www/public/images \
    -v /var/www/html/pivot/.env:/var/www/.env \
    --restart unless-stopped \
    pivot-app:latest
  
  echo "✅ Application déployée sur le port 4004!"
else
  echo "❌ Échec du build. Code de sortie: $BUILD_STATUS"
  exit $BUILD_STATUS
fi 