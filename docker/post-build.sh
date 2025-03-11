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