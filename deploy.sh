#!/bin/bash
set -e

# Répertoire du projet
PROJECT_DIR="/chemin/vers/votre/projet"
cd $PROJECT_DIR

# Mise à jour du code depuis Git
echo "Mise à jour du code depuis la branche miseEnProd..."
git fetch
git checkout miseEnProd
git pull

# Préparation du fichier .env si nécessaire
if [ ! -f .env ]; then
    echo "Création du fichier .env..."
    cp .env.example .env
    # Ajoutez ici la configuration des variables d'environnement si nécessaire
fi

# Démarrage des conteneurs
echo "Redémarrage des conteneurs Docker..."
docker-compose down
docker-compose build
docker-compose up -d

echo "Déploiement terminé avec succès!" 