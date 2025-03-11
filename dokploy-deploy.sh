#!/bin/bash

# Script pour faciliter le déploiement avec dokploy

echo "Démarrage du déploiement avec dokploy..."

# Vérifier si dokploy est installé
if ! command -v dokploy &> /dev/null; then
    echo "dokploy n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

# Construire l'image Docker
echo "Construction de l'image Docker..."
docker build -t pivot-app:latest --target pivot-app .

# Déployer avec dokploy
echo "Déploiement avec dokploy..."
dokploy deploy

echo "Déploiement terminé!" 