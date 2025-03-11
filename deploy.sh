#!/bin/bash

# Script de déploiement pour l'application Laravel

echo "🚀 Démarrage du déploiement..."

# Vérifier si Docker est installé
if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé. Veuillez l'installer avant de continuer."
    exit 1
fi

# Vérifier si les répertoires nécessaires existent
echo "🔍 Vérification des répertoires..."
mkdir -p /var/www/html/pivot/storage/app/public
mkdir -p /var/www/html/pivot/storage/framework/cache
mkdir -p /var/www/html/pivot/storage/framework/sessions
mkdir -p /var/www/html/pivot/storage/framework/views
mkdir -p /var/www/html/pivot/storage/logs
mkdir -p /var/www/html/pivot/public/images

# Vérifier si le fichier .env existe
if [ ! -f "/var/www/html/pivot/.env" ]; then
    echo "⚠️ Le fichier .env n'existe pas. Création d'un fichier .env par défaut..."
    cp .env.example /var/www/html/pivot/.env
fi

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

# Lancer le nouveau conteneur avec des options de débogage
echo "🚀 Lancement du nouveau conteneur..."
docker run -d --name pivot-app \
  -p 4004:4004 \
  -v /var/www/html/pivot/storage:/var/www/storage \
  -v /var/www/html/pivot/public/images:/var/www/public/images \
  -v /var/www/html/pivot/.env:/var/www/.env \
  --restart unless-stopped \
  pivot-app:latest

# Attendre que le conteneur démarre
echo "⏳ Attente du démarrage du conteneur..."
sleep 5

# Vérifier si le conteneur est en cours d'exécution
if [ "$(docker ps -q -f name=pivot-app)" ]; then
  echo "✅ Application déployée avec succès sur le port 4004!"
  
  # Afficher les logs pour le débogage
  echo "📊 Logs du conteneur (10 dernières lignes):"
  docker logs pivot-app --tail 10
  
  # Vérifier si Nginx est en cours d'exécution dans le conteneur
  echo "🔍 Vérification de l'état de Nginx..."
  if docker exec pivot-app ps aux | grep -q "[n]ginx"; then
    echo "✅ Nginx est en cours d'exécution."
  else
    echo "❌ Nginx n'est pas en cours d'exécution!"
    echo "📊 Logs complets du conteneur:"
    docker logs pivot-app
  fi
  
  # Vérifier si PHP-FPM est en cours d'exécution dans le conteneur
  echo "🔍 Vérification de l'état de PHP-FPM..."
  if docker exec pivot-app ps aux | grep -q "[p]hp-fpm"; then
    echo "✅ PHP-FPM est en cours d'exécution."
  else
    echo "❌ PHP-FPM n'est pas en cours d'exécution!"
    echo "📊 Logs complets du conteneur:"
    docker logs pivot-app
  fi
  
  echo "🌐 L'application devrait être accessible à l'adresse http://votre-serveur:4004"
else
  echo "❌ Échec du déploiement. Le conteneur n'est pas en cours d'exécution."
  echo "📊 Logs du conteneur:"
  docker logs pivot-app
  exit 1
fi 