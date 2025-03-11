#!/bin/bash

# Script pour inspecter et corriger le problème "select-a-container" dans dokploy

echo "Inspection et correction du problème 'select-a-container' dans dokploy..."

# Vérifier si sudo est disponible
if command -v sudo &> /dev/null; then
    SUDO="sudo"
else
    SUDO=""
fi

# Rechercher "select-a-container" dans les fichiers de dokploy
echo "Recherche de 'select-a-container' dans les fichiers de dokploy..."
if [ -d "/etc/dokploy" ]; then
    $SUDO find /etc/dokploy -type f -exec grep -l "select-a-container" {} \; 2>/dev/null
    
    # Rechercher dans les fichiers YAML
    echo "Recherche dans les fichiers YAML de dokploy..."
    $SUDO find /etc/dokploy -name "*.yml" -o -name "*.yaml" | while read file; do
        echo "Contenu de $file:"
        $SUDO cat "$file"
        echo "----------------------------------------"
    done
else
    echo "Répertoire dokploy non trouvé à /etc/dokploy"
fi

# Rechercher dans les fichiers de configuration Docker
echo "Recherche dans les fichiers de configuration Docker..."
if [ -d "/etc/docker" ]; then
    $SUDO find /etc/docker -type f -exec grep -l "select-a-container" {} \; 2>/dev/null
else
    echo "Répertoire Docker non trouvé à /etc/docker"
fi

# Vérifier les conteneurs Docker avec un nom similaire
echo "Vérification des conteneurs Docker avec un nom similaire..."
docker ps -a | grep -i "select"

# Vérifier les images Docker avec un nom similaire
echo "Vérification des images Docker avec un nom similaire..."
docker images | grep -i "select"

# Vérifier les réseaux Docker
echo "Vérification des réseaux Docker..."
docker network ls

# Vérifier les volumes Docker
echo "Vérification des volumes Docker..."
docker volume ls

# Vérifier les processus dokploy en cours d'exécution
echo "Vérification des processus dokploy en cours d'exécution..."
ps aux | grep -i "dokploy"

# Vérifier les logs système pour dokploy
echo "Vérification des logs système pour dokploy..."
if [ -f "/var/log/syslog" ]; then
    $SUDO grep -i "dokploy" /var/log/syslog | tail -n 50
else
    echo "Fichier de log système non trouvé à /var/log/syslog"
fi

# Vérifier les logs Docker
echo "Vérification des logs Docker..."
docker logs $(docker ps -q) 2>/dev/null | grep -i "select-a-container"

# Créer un conteneur nommé "select-a-container" pour voir si cela résout le problème
echo "Création d'un conteneur nommé 'select-a-container'..."
docker run -d --name select-a-container alpine:latest sleep 3600 || true

echo "Inspection terminée!" 