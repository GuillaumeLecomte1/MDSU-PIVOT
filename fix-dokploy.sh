#!/bin/bash

# Script pour vérifier et corriger les problèmes de dokploy

echo "Vérification et correction des problèmes de dokploy..."

# Vérifier si dokploy est installé
if ! command -v dokploy &> /dev/null; then
    echo "dokploy n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

# Vérifier les conteneurs en cours d'exécution
echo "Conteneurs en cours d'exécution:"
docker ps

# Vérifier les conteneurs arrêtés
echo "Conteneurs arrêtés:"
docker ps -a

# Nettoyer les conteneurs avec "select-a-container"
echo "Nettoyage des conteneurs problématiques..."
docker ps -a | grep "select-a-container" && docker rm -f $(docker ps -a | grep "select-a-container" | awk '{print $1}') || echo "Aucun conteneur 'select-a-container' trouvé."

# Vérifier les images Docker
echo "Images Docker:"
docker images

# Vérifier la configuration dokploy
echo "Configuration dokploy:"
cat .dokploy.yml

# Vérifier les logs dokploy
echo "Logs dokploy:"
if [ -f "/var/log/dokploy/dokploy.log" ]; then
    tail -n 50 /var/log/dokploy/dokploy.log
else
    echo "Fichier de log dokploy non trouvé."
fi

# Redémarrer le service dokploy si nécessaire
echo "Redémarrage du service dokploy..."
if command -v systemctl &> /dev/null && systemctl list-unit-files | grep -q dokploy; then
    sudo systemctl restart dokploy
    echo "Service dokploy redémarré."
else
    echo "Service dokploy non trouvé ou systemctl non disponible."
fi

echo "Vérification terminée!" 