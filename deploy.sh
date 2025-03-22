#!/bin/bash
set -e

# Couleurs pour les messages
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Date et version
BUILD_DATE=$(date +"%Y-%m-%d")
GIT_HASH=$(git rev-parse --short HEAD 2>/dev/null || echo "unknown")
VERSION="${BUILD_DATE}-${GIT_HASH}"

print_header() {
  echo -e "${BLUE}===============================================${NC}"
  echo -e "${BLUE}= $1${NC}"
  echo -e "${BLUE}===============================================${NC}"
}

print_header "Déploiement de l'application Pivot (${VERSION})"

# Options par défaut
BUILDKIT_ENABLED=1
PUSH_IMAGE=0
REMOTE_DEPLOY=0
SERVER_HOST="ubuntu@guillaume-lcte.fr"
APP_ENV="production"
APP_DEBUG="false"
NODE_VERSION="20"
CACHE_ENABLED=1
PRUNE_IMAGES=0
VERBOSE=0
SKIP_ARTISAN_COMMANDS=false
CHECK_PHP_SYNTAX_ONLY=false

# Aide
show_help() {
  echo -e "${YELLOW}Options disponibles:${NC}"
  echo -e "  --help                  Affiche l'aide"
  echo -e "  --push                  Pousse l'image vers le registre Docker"
  echo -e "  --remote-deploy         Déploie sur le serveur distant"
  echo -e "  --server=HOST           Spécifie le serveur de déploiement (défaut: ${SERVER_HOST})"
  echo -e "  --env=ENV               Spécifie l'environnement (défaut: ${APP_ENV})"
  echo -e "  --debug                 Active le mode debug"
  echo -e "  --node-version=VER      Spécifie la version de Node.js (défaut: ${NODE_VERSION})"
  echo -e "  --no-cache              Désactive le cache Docker"
  echo -e "  --prune                 Nettoie les images Docker non utilisées"
  echo -e "  --verbose               Affiche plus de détails"
  echo -e "  --skip-artisan          Ignore les commandes Artisan pendant le build"
  echo -e "  --syntax-only           Vérifie uniquement la syntaxe PHP sans exécuter les commandes Artisan"
  exit 0
}

# Analyser les arguments
for arg in "$@"; do
  case $arg in
    --help)
      show_help
      ;;
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
    --env=*)
      APP_ENV="${arg#*=}"
      shift
      ;;
    --debug)
      APP_DEBUG="true"
      shift
      ;;
    --node-version=*)
      NODE_VERSION="${arg#*=}"
      shift
      ;;
    --no-cache)
      CACHE_ENABLED=0
      shift
      ;;
    --prune)
      PRUNE_IMAGES=1
      shift
      ;;
    --verbose)
      VERBOSE=1
      shift
      ;;
    --skip-artisan)
      SKIP_ARTISAN_COMMANDS=true
      shift
      ;;
    --syntax-only)
      CHECK_PHP_SYNTAX_ONLY=true
      SKIP_ARTISAN_COMMANDS=true
      shift
      ;;
  esac
done

# Options de build
BUILD_OPTS=""
if [ $CACHE_ENABLED -eq 0 ]; then
  BUILD_OPTS="--no-cache"
fi

if [ $VERBOSE -eq 1 ]; then
  BUILD_OPTS="$BUILD_OPTS --progress=plain"
else
  BUILD_OPTS="$BUILD_OPTS --progress=auto"
fi

# 1. Construction de l'image Docker
print_header "Construction de l'image Docker"
echo -e "${YELLOW}Environnement: ${NC}${APP_ENV}"
echo -e "${YELLOW}Debug: ${NC}${APP_DEBUG}"
echo -e "${YELLOW}Node.js: ${NC}${NODE_VERSION}"
echo -e "${YELLOW}Ignorer les commandes Artisan: ${NC}${SKIP_ARTISAN_COMMANDS}"

if [ "$CHECK_PHP_SYNTAX_ONLY" = "true" ]; then
  echo -e "${YELLOW}Mode vérification syntaxe uniquement${NC}"
fi

DOCKER_BUILDKIT=$BUILDKIT_ENABLED docker build $BUILD_OPTS \
  --build-arg BUILDKIT_INLINE_CACHE=$CACHE_ENABLED \
  --build-arg APP_ENV=$APP_ENV \
  --build-arg NODE_VERSION=$NODE_VERSION \
  --build-arg APP_DEBUG=$APP_DEBUG \
  --build-arg SKIP_ARTISAN_COMMANDS=$SKIP_ARTISAN_COMMANDS \
  --tag pivot-app:${VERSION} \
  --tag pivot-app:latest \
  .

# 2. Enregistrer le tag actuel
echo $VERSION > .version
echo -e "${GREEN}Image construite avec succès: ${NC}pivot-app:${VERSION}"

# 3. Push de l'image
if [ $PUSH_IMAGE -eq 1 ]; then
  print_header "Push de l'image vers le registre Docker"
  
  # Remplacez par l'URL de votre registre Docker
  docker tag pivot-app:${VERSION} registry.example.com/pivot-app:${VERSION}
  docker tag pivot-app:${VERSION} registry.example.com/pivot-app:latest
  
  # Vérifier si l'authentification est nécessaire
  if [ -z "$DOCKER_USERNAME" ] || [ -z "$DOCKER_PASSWORD" ]; then
    echo -e "${YELLOW}Variables d'authentification Docker non définies. Connexion interactive possible.${NC}"
  else
    echo -e "${YELLOW}Connexion au registre Docker...${NC}"
    echo "$DOCKER_PASSWORD" | docker login --username "$DOCKER_USERNAME" --password-stdin
  fi
  
  docker push registry.example.com/pivot-app:${VERSION}
  docker push registry.example.com/pivot-app:latest
  echo -e "${GREEN}Image publiée avec succès: ${NC}registry.example.com/pivot-app:${VERSION}"
fi

# 4. Déploiement distant
if [ $REMOTE_DEPLOY -eq 1 ] && [ "$CHECK_PHP_SYNTAX_ONLY" != "true" ]; then
  print_header "Déploiement sur le serveur distant ${SERVER_HOST}"
  
  # Préparation du docker-compose.yml avec les variables d'environnement
  TMP_COMPOSE=$(mktemp)
  cat docker-compose.yml | sed "s/\${APP_DEBUG:-false}/${APP_DEBUG}/g" > $TMP_COMPOSE
  
  # Copier les fichiers nécessaires
  echo -e "${YELLOW}Copie des fichiers...${NC}"
  scp $TMP_COMPOSE $SERVER_HOST:/tmp/docker-compose.yml
  scp -r docker/entrypoint.sh $SERVER_HOST:/tmp/entrypoint.sh
  
  # Nettoyer le fichier temporaire
  rm $TMP_COMPOSE
  
  # Exécuter les commandes sur le serveur distant
  echo -e "${YELLOW}Exécution des commandes sur le serveur distant...${NC}"
  ssh $SERVER_HOST "cd /tmp && \
    echo 'Vérification des volumes et réseaux...' && \
    docker network create pivot-network 2>/dev/null || true && \
    echo 'Arrêt des conteneurs existants...' && \
    docker-compose down && \
    echo 'Démarrage des nouveaux conteneurs...' && \
    docker-compose up -d && \
    echo 'Vérification des conteneurs...' && \
    docker-compose ps && \
    echo 'Affichage des logs initiaux...' && \
    docker-compose logs --tail=10"
  
  if [ $PRUNE_IMAGES -eq 1 ]; then
    echo -e "${YELLOW}Nettoyage des images inutilisées...${NC}"
    ssh $SERVER_HOST "docker system prune -f --volumes"
  fi
  
  echo -e "${GREEN}Déploiement terminé avec succès!${NC}"
elif [ "$CHECK_PHP_SYNTAX_ONLY" = "true" ]; then
  print_header "Mode vérification de syntaxe uniquement - déploiement ignoré"
fi

print_header "Processus terminé"

# Afficher des informations utiles
echo -e "${YELLOW}Pour tester localement: ${NC}docker-compose up -d"
echo -e "${YELLOW}Pour déployer: ${NC}./deploy.sh --remote-deploy"
echo -e "${YELLOW}Pour déboguer: ${NC}./deploy.sh --env=development --debug"
echo -e "${YELLOW}Pour vérifier la syntaxe PHP uniquement: ${NC}./deploy.sh --syntax-only" 