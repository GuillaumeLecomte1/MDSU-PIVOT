#!/bin/bash

# Script pour résoudre le problème "No such container: select-a-container" avec dokploy

echo "Résolution du problème 'No such container: select-a-container'..."

# Vérifier si dokploy est installé
if ! command -v dokploy &> /dev/null; then
    echo "dokploy n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

# Arrêter et supprimer tous les conteneurs en cours d'exécution
echo "Arrêt et suppression de tous les conteneurs en cours d'exécution..."
docker ps -a -q | xargs -r docker stop
docker ps -a -q | xargs -r docker rm

# Supprimer toutes les images
echo "Suppression de toutes les images..."
docker images -q | xargs -r docker rmi -f

# Nettoyer les volumes Docker non utilisés
echo "Nettoyage des volumes Docker non utilisés..."
docker volume prune -f

# Nettoyer les réseaux Docker non utilisés
echo "Nettoyage des réseaux Docker non utilisés..."
docker network prune -f

# Nettoyer le système Docker
echo "Nettoyage du système Docker..."
docker system prune -f

# Vérifier les fichiers de configuration dokploy
echo "Vérification des fichiers de configuration dokploy..."
if [ -f "/etc/dokploy/config.yml" ]; then
    echo "Configuration dokploy trouvée à /etc/dokploy/config.yml"
    cat /etc/dokploy/config.yml
else
    echo "Configuration dokploy non trouvée à /etc/dokploy/config.yml"
fi

# Vérifier les applications dokploy
echo "Vérification des applications dokploy..."
if [ -d "/etc/dokploy/applications" ]; then
    echo "Applications dokploy trouvées à /etc/dokploy/applications"
    ls -la /etc/dokploy/applications
else
    echo "Répertoire des applications dokploy non trouvé à /etc/dokploy/applications"
fi

# Reconstruire l'image Docker
echo "Reconstruction de l'image Docker..."
docker build -t pivot-app:latest --target pivot-app .

# Créer un conteneur temporaire
echo "Création d'un conteneur temporaire..."
docker run --name pivot-app -d pivot-app:latest

# Vérifier si le conteneur est en cours d'exécution
if [ "$(docker ps -q -f name=pivot-app)" ]; then
    echo "Conteneur temporaire créé avec succès."
else
    echo "Erreur: Le conteneur temporaire n'a pas pu être créé."
    exit 1
fi

# Déployer avec dokploy
echo "Déploiement avec dokploy..."
dokploy deploy

echo "Résolution terminée!" 