#!/bin/bash

# Script pour faciliter le déploiement avec dokploy

echo "Démarrage du déploiement avec dokploy..."

# Vérifier si dokploy est installé
if ! command -v dokploy &> /dev/null; then
    echo "dokploy n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

# Nettoyer les anciens conteneurs et images
echo "Nettoyage des anciens conteneurs et images..."
docker ps -a | grep pivot-app && docker rm -f pivot-app || true
docker images | grep pivot-app && docker rmi -f pivot-app:latest || true

# Construire l'image Docker avec un timeout plus long
echo "Construction de l'image Docker..."
DOCKER_BUILDKIT=1 docker build --progress=plain --no-cache -t pivot-app:latest --target pivot-app .

# Vérifier si la construction a réussi
if [ $? -ne 0 ]; then
    echo "Erreur lors de la construction de l'image Docker."
    exit 1
fi

# Créer un conteneur temporaire pour vérifier les images
echo "Vérification des images dans le conteneur..."
docker run --name pivot-app-temp -d pivot-app:latest
sleep 5

# Vérifier si le conteneur est en cours d'exécution
if [ "$(docker ps -q -f name=pivot-app-temp)" ]; then
    echo "Conteneur temporaire créé avec succès."
    
    # Vérifier les images placeholder
    docker exec pivot-app-temp ls -la /var/www/public/images/
    docker exec pivot-app-temp ls -la /var/www/public/build/images/
    
    # Arrêter et supprimer le conteneur temporaire
    docker stop pivot-app-temp
    docker rm pivot-app-temp
else
    echo "Erreur: Le conteneur temporaire n'a pas pu être créé."
    exit 1
fi

# Déployer avec dokploy
echo "Déploiement avec dokploy..."
dokploy deploy

echo "Déploiement terminé!" 