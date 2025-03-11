#!/bin/bash

# Script pour tester si dokploy fonctionne correctement avec un conteneur simple

echo "Test de dokploy avec un conteneur simple..."

# Vérifier si dokploy est installé
if ! command -v dokploy &> /dev/null; then
    echo "dokploy n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

# Créer un répertoire temporaire pour le test
echo "Création d'un répertoire temporaire pour le test..."
TEST_DIR=$(mktemp -d)
cd $TEST_DIR

# Créer un Dockerfile simple
echo "Création d'un Dockerfile simple..."
cat > Dockerfile << EOF
FROM alpine:latest AS test-container
CMD ["echo", "Hello from test container"]
EOF

# Créer un fichier de configuration dokploy
echo "Création d'un fichier de configuration dokploy..."
cat > .dokploy.yml << EOF
version: '1'

app:
  name: test-app
  container: test-container
  image: test-container:latest
  build:
    context: .
    dockerfile: Dockerfile
    target: test-container
EOF

# Construire l'image Docker
echo "Construction de l'image Docker..."
docker build -t test-container:latest --target test-container .

# Déployer avec dokploy
echo "Déploiement avec dokploy..."
dokploy deploy

# Vérifier si le déploiement a réussi
if [ $? -eq 0 ]; then
    echo "✅ Le déploiement avec dokploy a réussi."
else
    echo "❌ Le déploiement avec dokploy a échoué."
fi

# Nettoyer
echo "Nettoyage..."
cd -
rm -rf $TEST_DIR
docker ps -a | grep test-container && docker rm -f $(docker ps -a | grep test-container | awk '{print $1}')
docker images | grep test-container && docker rmi -f test-container:latest

echo "Test terminé!" 