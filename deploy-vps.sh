#!/bin/bash

echo "🚀 Déploiement sur le VPS (guillaume-lcte.fr)..."

# Configuration
VPS_USER="ubuntu"
VPS_HOST="guillaume-lcte.fr"
CONTAINER_NAME="pivot-app"
IMAGE_NAME="pivot-app:latest"

# Connexion SSH et nettoyage des conteneurs existants
echo "🧹 Nettoyage des conteneurs existants..."
ssh $VPS_USER@$VPS_HOST << EOF
    # Arrêter et supprimer les conteneurs existants
    docker ps -a -q | xargs -r docker rm -f
    
    # Supprimer les images existantes
    docker images | grep "pivot-app" | awk '{print \$3}' | xargs -r docker rmi -f
EOF

# Construction de l'image localement
echo "🏗️ Construction de l'image Docker..."
docker build -t $IMAGE_NAME .

# Sauvegarde de l'image
echo "💾 Sauvegarde de l'image Docker..."
docker save $IMAGE_NAME | gzip > pivot-app.tar.gz

# Transfert de l'image vers le VPS
echo "📤 Transfert de l'image vers le VPS..."
scp pivot-app.tar.gz $VPS_USER@$VPS_HOST:~/

# Chargement et déploiement sur le VPS
echo "📥 Chargement et déploiement sur le VPS..."
ssh $VPS_USER@$VPS_HOST << EOF
    # Charger l'image
    docker load < pivot-app.tar.gz
    
    # Démarrer le conteneur
    docker run -d \
        --name $CONTAINER_NAME \
        -p 4004:4004 \
        --restart unless-stopped \
        $IMAGE_NAME

    # Nettoyage
    rm pivot-app.tar.gz
    
    # Vérifier le statut
    docker ps | grep $CONTAINER_NAME
EOF

# Nettoyage local
echo "🧹 Nettoyage local..."
rm pivot-app.tar.gz

echo "✅ Déploiement terminé !" 