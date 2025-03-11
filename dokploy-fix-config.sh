#!/bin/bash

# Script pour vérifier et corriger la configuration de dokploy

echo "Vérification et correction de la configuration dokploy..."

# Vérifier si sudo est disponible
if command -v sudo &> /dev/null; then
    SUDO="sudo"
else
    SUDO=""
fi

# Vérifier si le répertoire dokploy existe
if [ -d "/etc/dokploy" ]; then
    echo "Répertoire dokploy trouvé à /etc/dokploy"
    
    # Vérifier les permissions
    echo "Vérification des permissions..."
    ls -la /etc/dokploy
    
    # Corriger les permissions si nécessaire
    echo "Correction des permissions..."
    $SUDO chmod -R 755 /etc/dokploy
    $SUDO chown -R $(whoami):$(whoami) /etc/dokploy
    
    # Vérifier la configuration
    if [ -f "/etc/dokploy/config.yml" ]; then
        echo "Configuration trouvée à /etc/dokploy/config.yml"
        cat /etc/dokploy/config.yml
        
        # Sauvegarder la configuration existante
        echo "Sauvegarde de la configuration existante..."
        $SUDO cp /etc/dokploy/config.yml /etc/dokploy/config.yml.bak
        
        # Créer une nouvelle configuration
        echo "Création d'une nouvelle configuration..."
        cat > /tmp/dokploy-config.yml << EOF
docker:
  default_container: pivot-app
  default_image: pivot-app:latest
  default_port: 4004
  default_target: pivot-app
EOF
        $SUDO cp /tmp/dokploy-config.yml /etc/dokploy/config.yml
    else
        echo "Configuration non trouvée à /etc/dokploy/config.yml"
        echo "Création d'une nouvelle configuration..."
        cat > /tmp/dokploy-config.yml << EOF
docker:
  default_container: pivot-app
  default_image: pivot-app:latest
  default_port: 4004
  default_target: pivot-app
EOF
        $SUDO mkdir -p /etc/dokploy
        $SUDO cp /tmp/dokploy-config.yml /etc/dokploy/config.yml
    fi
    
    # Vérifier les applications
    if [ -d "/etc/dokploy/applications" ]; then
        echo "Répertoire des applications trouvé à /etc/dokploy/applications"
        ls -la /etc/dokploy/applications
        
        # Vérifier s'il y a une application pivot
        if [ -d "/etc/dokploy/applications/pivot" ]; then
            echo "Application pivot trouvée à /etc/dokploy/applications/pivot"
            ls -la /etc/dokploy/applications/pivot
            
            # Vérifier la configuration de l'application
            if [ -f "/etc/dokploy/applications/pivot/config.yml" ]; then
                echo "Configuration de l'application trouvée à /etc/dokploy/applications/pivot/config.yml"
                cat /etc/dokploy/applications/pivot/config.yml
                
                # Sauvegarder la configuration existante
                echo "Sauvegarde de la configuration existante..."
                $SUDO cp /etc/dokploy/applications/pivot/config.yml /etc/dokploy/applications/pivot/config.yml.bak
                
                # Créer une nouvelle configuration
                echo "Création d'une nouvelle configuration..."
                cat > /tmp/pivot-config.yml << EOF
container: pivot-app
image: pivot-app:latest
port: 4004
build:
  context: .
  dockerfile: Dockerfile
  target: pivot-app
EOF
                $SUDO cp /tmp/pivot-config.yml /etc/dokploy/applications/pivot/config.yml
            else
                echo "Configuration de l'application non trouvée à /etc/dokploy/applications/pivot/config.yml"
                echo "Création d'une nouvelle configuration..."
                cat > /tmp/pivot-config.yml << EOF
container: pivot-app
image: pivot-app:latest
port: 4004
build:
  context: .
  dockerfile: Dockerfile
  target: pivot-app
EOF
                $SUDO mkdir -p /etc/dokploy/applications/pivot
                $SUDO cp /tmp/pivot-config.yml /etc/dokploy/applications/pivot/config.yml
            fi
        else
            echo "Application pivot non trouvée à /etc/dokploy/applications/pivot"
            echo "Création d'une nouvelle application..."
            $SUDO mkdir -p /etc/dokploy/applications/pivot
            cat > /tmp/pivot-config.yml << EOF
container: pivot-app
image: pivot-app:latest
port: 4004
build:
  context: .
  dockerfile: Dockerfile
  target: pivot-app
EOF
            $SUDO cp /tmp/pivot-config.yml /etc/dokploy/applications/pivot/config.yml
        fi
    else
        echo "Répertoire des applications non trouvé à /etc/dokploy/applications"
        echo "Création du répertoire des applications..."
        $SUDO mkdir -p /etc/dokploy/applications/pivot
        cat > /tmp/pivot-config.yml << EOF
container: pivot-app
image: pivot-app:latest
port: 4004
build:
  context: .
  dockerfile: Dockerfile
  target: pivot-app
EOF
        $SUDO cp /tmp/pivot-config.yml /etc/dokploy/applications/pivot/config.yml
    fi
else
    echo "Répertoire dokploy non trouvé à /etc/dokploy"
    echo "Création du répertoire dokploy..."
    $SUDO mkdir -p /etc/dokploy/applications/pivot
    
    # Créer la configuration principale
    cat > /tmp/dokploy-config.yml << EOF
docker:
  default_container: pivot-app
  default_image: pivot-app:latest
  default_port: 4004
  default_target: pivot-app
EOF
    $SUDO cp /tmp/dokploy-config.yml /etc/dokploy/config.yml
    
    # Créer la configuration de l'application
    cat > /tmp/pivot-config.yml << EOF
container: pivot-app
image: pivot-app:latest
port: 4004
build:
  context: .
  dockerfile: Dockerfile
  target: pivot-app
EOF
    $SUDO cp /tmp/pivot-config.yml /etc/dokploy/applications/pivot/config.yml
fi

echo "Configuration dokploy corrigée!" 