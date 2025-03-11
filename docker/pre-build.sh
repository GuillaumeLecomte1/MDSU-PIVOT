#!/bin/bash

# Script pour arrêter temporairement les autres conteneurs pendant le build
# afin de libérer des ressources sur le VPS

echo "🔍 Recherche des conteneurs en cours d'exécution (sauf pivot-app)..."

# Nettoyer les images et conteneurs inutilisés pour libérer de l'espace disque
echo "🧹 Nettoyage des ressources Docker inutilisées..."
docker system prune -f --volumes

# Nettoyer les images non utilisées
echo "🧹 Suppression des images non utilisées..."
docker image prune -a -f

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

# Libérer le cache système
echo "🧠 Libération de la mémoire cache système..."
sync && echo 3 > /proc/sys/vm/drop_caches || true

# Afficher les ressources disponibles
echo "📊 Ressources disponibles pour le build:"
free -h
df -h 