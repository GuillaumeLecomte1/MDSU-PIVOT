#!/bin/bash

# Script pour arrêter temporairement les autres conteneurs pendant le build
# afin de libérer des ressources sur le VPS

echo "🔍 Recherche des conteneurs en cours d'exécution (sauf pivot-app)..."

# Obtenir la liste des conteneurs en cours d'exécution (sauf pivot-app)
CONTAINERS=$(docker ps --format "{{.Names}}" | grep -v "pivot-app")

# Sauvegarder la liste des conteneurs à redémarrer
echo "$CONTAINERS" > /tmp/containers_to_restart.txt

# Arrêter les conteneurs
if [ -n "$CONTAINERS" ]; then
    echo "🛑 Arrêt temporaire des conteneurs suivants pour libérer des ressources:"
    echo "$CONTAINERS"
    
    for CONTAINER in $CONTAINERS; do
        echo "  - Arrêt de $CONTAINER"
        docker stop "$CONTAINER"
    done
    
    echo "✅ Conteneurs arrêtés avec succès. Ils seront redémarrés après le build."
else
    echo "ℹ️ Aucun autre conteneur en cours d'exécution à arrêter."
fi 