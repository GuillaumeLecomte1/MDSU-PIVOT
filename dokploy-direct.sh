#!/bin/bash

# Script pour déployer directement sans passer par dokploy

echo "Déploiement direct sans dokploy..."

# Arrêter et supprimer tous les conteneurs en cours d'exécution
echo "Arrêt et suppression de tous les conteneurs en cours d'exécution..."
docker ps -a -q | xargs -r docker stop
docker ps -a -q | xargs -r docker rm

# Supprimer l'image pivot-app si elle existe
echo "Suppression de l'image pivot-app si elle existe..."
docker images | grep pivot-app && docker rmi -f pivot-app:latest || true

# Construire l'image Docker
echo "Construction de l'image Docker..."
docker build -t pivot-app:latest --target pivot-app .

# Vérifier si la construction a réussi
if [ $? -ne 0 ]; then
    echo "Erreur lors de la construction de l'image Docker."
    exit 1
fi

# Créer et démarrer le conteneur
echo "Création et démarrage du conteneur..."
docker run -d --name pivot-app -p 4004:4004 pivot-app:latest

# Vérifier si le conteneur est en cours d'exécution
if [ "$(docker ps -q -f name=pivot-app)" ]; then
    echo "✅ Conteneur pivot-app créé et démarré avec succès."
    echo "Le conteneur est accessible sur le port 4004."
    
    # Afficher les logs du conteneur
    echo "Logs du conteneur:"
    docker logs pivot-app
else
    echo "❌ Erreur: Le conteneur n'a pas pu être créé ou démarré."
    exit 1
fi

echo "Déploiement direct terminé!" 