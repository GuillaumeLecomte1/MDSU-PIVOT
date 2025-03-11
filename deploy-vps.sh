#!/bin/bash

# Script de déploiement personnalisé pour le VPS
# Ce script est conçu pour être exécuté sur le VPS après le déploiement par dokploy

# Définir les variables
APP_DIR="/var/www/html/pivot"
CONTAINER_NAME="pivot-app"
IMAGE_NAME="pivot-app:latest"

# Afficher un message de début
echo "🚀 Déploiement de l'application sur le VPS..."

# Vérifier si le conteneur existe déjà
if [ "$(docker ps -a -q -f name=$CONTAINER_NAME)" ]; then
    echo "🛑 Arrêt et suppression du conteneur existant..."
    docker stop $CONTAINER_NAME
    docker rm $CONTAINER_NAME
fi

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

# Vérifier si l'image placeholder.jpg existe
if [ ! -f $APP_DIR/public/images/placeholder.jpg ]; then
    echo "🖼️ Création de l'image placeholder.jpg..."
    touch $APP_DIR/public/images/placeholder.jpg
fi

# Construire l'image Docker
echo "🔨 Construction de l'image Docker..."
docker build -t $IMAGE_NAME .

# Vérifier si la construction a réussi
if [ $? -ne 0 ]; then
    echo "❌ Erreur lors de la construction de l'image Docker."
    exit 1
fi

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
    
    # Afficher les logs du conteneur
    echo "📋 Logs du conteneur :"
    docker logs $CONTAINER_NAME
else
    echo "❌ Erreur lors du lancement du conteneur."
    echo "📋 Logs du conteneur :"
    docker logs $CONTAINER_NAME
    exit 1
fi

echo "✅ Déploiement terminé avec succès !"
exit 0 