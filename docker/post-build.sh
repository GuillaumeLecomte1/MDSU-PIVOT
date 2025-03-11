#!/bin/bash

# Script pour redémarrer les conteneurs après le build

echo "🔄 Redémarrage des conteneurs arrêtés précédemment..."

# Vérifier si le fichier existe
if [ -f "/tmp/containers_to_restart.txt" ]; then
    # Lire la liste des conteneurs à redémarrer
    CONTAINERS=$(cat /tmp/containers_to_restart.txt)
    
    # Redémarrer les conteneurs
    if [ -n "$CONTAINERS" ]; then
        echo "🚀 Redémarrage des conteneurs suivants:"
        echo "$CONTAINERS"
        
        for CONTAINER in $CONTAINERS; do
            echo "  - Redémarrage de $CONTAINER"
            docker start "$CONTAINER"
        done
        
        echo "✅ Conteneurs redémarrés avec succès."
    else
        echo "ℹ️ Aucun conteneur à redémarrer."
    fi
    
    # Supprimer le fichier temporaire
    rm -f /tmp/containers_to_restart.txt
else
    echo "ℹ️ Aucun conteneur à redémarrer (fichier non trouvé)."
fi

# Nettoyer les images intermédiaires et le cache de build
echo "🧹 Nettoyage des images intermédiaires et du cache de build..."
docker image prune -f

# Vérifier l'état du conteneur pivot-app
echo "🔍 Vérification de l'état du conteneur pivot-app..."
docker ps | grep pivot-app

# Afficher les logs du conteneur pour vérifier le démarrage
echo "📋 Dernières lignes des logs du conteneur pivot-app:"
docker logs --tail 20 pivot-app 