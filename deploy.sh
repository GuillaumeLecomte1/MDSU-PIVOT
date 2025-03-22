#!/bin/bash
set -e

# Couleurs pour les messages
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Date et version
BUILD_DATE=$(date +"%Y-%m-%d")
GIT_HASH=$(git rev-parse --short HEAD)
VERSION="${BUILD_DATE}-${GIT_HASH}"

echo -e "${GREEN}=== Déploiement de l'application Pivot (${VERSION}) ===${NC}"

# Options
BUILDKIT_ENABLED=1
PUSH_IMAGE=0
REMOTE_DEPLOY=0
SERVER_HOST="ubuntu@guillaume-lcte.fr"

# Analyser les arguments
for arg in "$@"; do
  case $arg in
    --push)
      PUSH_IMAGE=1
      shift
      ;;
    --remote-deploy)
      REMOTE_DEPLOY=1
      shift
      ;;
    --server=*)
      SERVER_HOST="${arg#*=}"
      shift
      ;;
  esac
done

# 1. Construction de l'image Docker
echo -e "${YELLOW}=== Construction de l'image Docker ===${NC}"
DOCKER_BUILDKIT=$BUILDKIT_ENABLED docker build \
  --build-arg BUILDKIT_INLINE_CACHE=1 \
  --tag pivot-app:${VERSION} \
  --tag pivot-app:latest \
  .

# 2. Enregistrer le tag actuel
echo $VERSION > .version

if [ $PUSH_IMAGE -eq 1 ]; then
  echo -e "${YELLOW}=== Push de l'image vers le registre Docker ===${NC}"
  # Remplacez par l'URL de votre registre Docker
  docker tag pivot-app:${VERSION} registry.example.com/pivot-app:${VERSION}
  docker tag pivot-app:${VERSION} registry.example.com/pivot-app:latest
  docker push registry.example.com/pivot-app:${VERSION}
  docker push registry.example.com/pivot-app:latest
  echo -e "${GREEN}Image publiée avec succès: registry.example.com/pivot-app:${VERSION}${NC}"
fi

if [ $REMOTE_DEPLOY -eq 1 ]; then
  echo -e "${YELLOW}=== Déploiement sur le serveur distant ===${NC}"
  
  # Copier les fichiers nécessaires
  echo "Copie des fichiers..."
  scp docker-compose.yml $SERVER_HOST:/tmp/docker-compose.yml
  
  # Exécuter les commandes sur le serveur distant
  ssh $SERVER_HOST "cd /tmp && \
    echo 'Tirage de la dernière image...' && \
    docker-compose pull && \
    echo 'Arrêt des conteneurs existants...' && \
    docker-compose down && \
    echo 'Démarrage des nouveaux conteneurs...' && \
    docker-compose up -d && \
    echo 'Nettoyage des images inutilisées...' && \
    docker system prune -f"
  
  echo -e "${GREEN}Déploiement terminé avec succès!${NC}"
fi

echo -e "${GREEN}=== Processus terminé ===${NC}"

# Afficher des informations utiles
echo -e "${YELLOW}Pour tester localement: ${NC}docker-compose up -d"
echo -e "${YELLOW}Pour déployer: ${NC}./deploy.sh --remote-deploy" 