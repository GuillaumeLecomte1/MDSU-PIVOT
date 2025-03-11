#!/bin/bash

# Script de déploiement simplifié
echo "🚀 Déploiement de l'application..."

# Variables
APP_DIR="/var/www/html/pivot"
CONTAINER_NAME="pivot-app"
IMAGE_NAME="pivot-app:latest"

# Arrêter et supprimer le conteneur existant
echo "🛑 Arrêt et suppression du conteneur existant..."
docker stop $CONTAINER_NAME 2>/dev/null || true
docker rm $CONTAINER_NAME 2>/dev/null || true

# Nettoyer les images Docker non utilisées
echo "🧹 Nettoyage des images Docker non utilisées..."
docker system prune -f

# Créer les répertoires nécessaires
echo "📁 Création des répertoires nécessaires..."
mkdir -p $APP_DIR/storage/app/public
mkdir -p $APP_DIR/storage/framework/cache
mkdir -p $APP_DIR/storage/framework/sessions
mkdir -p $APP_DIR/storage/framework/views
mkdir -p $APP_DIR/storage/logs
mkdir -p $APP_DIR/public/images

# Créer l'image placeholder.jpg si elle n'existe pas
echo "🖼️ Vérification de l'image placeholder.jpg..."
if [ ! -f $APP_DIR/public/images/placeholder.jpg ]; then
    echo "Création de l'image placeholder.jpg..."
    touch $APP_DIR/public/images/placeholder.jpg
fi

# Définir les permissions correctes pour le dossier images
echo "🔒 Définition des permissions pour le dossier images..."
chmod -R 755 $APP_DIR/public/images
chown -R www-data:www-data $APP_DIR/public/images 2>/dev/null || true

# Construire l'image Docker
echo "🔨 Construction de l'image Docker..."
docker build -t $IMAGE_NAME .

# Lancer le conteneur
echo "🚀 Lancement du conteneur..."
docker run -d \
    --name $CONTAINER_NAME \
    -p 4004:4004 \
    -v $APP_DIR/storage:/var/www/storage \
    -v $APP_DIR/public/images:/var/www/public/images \
    -v $APP_DIR/.env:/var/www/.env \
    --restart unless-stopped \
    $IMAGE_NAME

# Vérifier si le conteneur est en cours d'exécution
if [ "$(docker ps -q -f name=$CONTAINER_NAME)" ]; then
    echo "✅ Le conteneur est en cours d'exécution."
else
    echo "❌ Erreur lors du lancement du conteneur."
    docker logs $CONTAINER_NAME
    exit 1
fi

echo "✅ Déploiement terminé !"
exit 0 