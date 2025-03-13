#!/bin/bash

ENV_FILE="/var/www/html/pivot/.env"

# Vérifier si le fichier .env existe
if [ -f "$ENV_FILE" ]; then
    echo "Vérification du fichier .env..."
    
    # Vérifier si BROADCAST_DRIVER est défini
    if grep -q "BROADCAST_DRIVER=" "$ENV_FILE"; then
        # Remplacer BROADCAST_DRIVER=pusher par BROADCAST_DRIVER=log
        sed -i 's/BROADCAST_DRIVER=pusher/BROADCAST_DRIVER=log/g' "$ENV_FILE"
        echo "BROADCAST_DRIVER mis à jour à 'log'"
    else
        # Ajouter BROADCAST_DRIVER=log s'il n'existe pas
        echo "BROADCAST_DRIVER=log" >> "$ENV_FILE"
        echo "BROADCAST_DRIVER ajouté avec la valeur 'log'"
    fi
    
    # Vérifier si les variables Pusher sont définies correctement
    if grep -q "PUSHER_APP_KEY=your_app_key" "$ENV_FILE" || grep -q "PUSHER_APP_KEY=\"your_app_key\"" "$ENV_FILE"; then
        # Remplacer les valeurs par défaut par null
        sed -i 's/PUSHER_APP_ID=your_app_id/PUSHER_APP_ID=null/g' "$ENV_FILE"
        sed -i 's/PUSHER_APP_KEY=your_app_key/PUSHER_APP_KEY=null/g' "$ENV_FILE"
        sed -i 's/PUSHER_APP_SECRET=your_app_secret/PUSHER_APP_SECRET=null/g' "$ENV_FILE"
        sed -i 's/PUSHER_APP_ID="your_app_id"/PUSHER_APP_ID=null/g' "$ENV_FILE"
        sed -i 's/PUSHER_APP_KEY="your_app_key"/PUSHER_APP_KEY=null/g' "$ENV_FILE"
        sed -i 's/PUSHER_APP_SECRET="your_app_secret"/PUSHER_APP_SECRET=null/g' "$ENV_FILE"
        echo "Variables Pusher mises à jour à 'null'"
    fi
    
    echo "Vérification terminée."
else
    echo "Le fichier .env n'existe pas à l'emplacement $ENV_FILE"
fi 