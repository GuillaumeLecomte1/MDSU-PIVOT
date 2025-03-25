#!/bin/bash
set -e

# Variables de couleur
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== Script de compilation des assets frontend ===${NC}"

# Vérifier si Node.js est installé
if ! command -v node &> /dev/null; then
    echo -e "${RED}Node.js n'est pas installé. Veuillez l'installer pour continuer.${NC}"
    exit 1
fi

# Vérifier la version de Node.js
NODE_VERSION=$(node -v | cut -d 'v' -f 2 | cut -d '.' -f 1)
if [ "$NODE_VERSION" -lt 18 ]; then
    echo -e "${RED}Node.js version 18+ est requis. Version actuelle: $(node -v)${NC}"
    exit 1
fi

# Vérifier si npm est installé
if ! command -v npm &> /dev/null; then
    echo -e "${RED}npm n'est pas installé. Veuillez l'installer pour continuer.${NC}"
    exit 1
fi

# Installer les dépendances
echo -e "${YELLOW}Installation des dépendances npm...${NC}"
npm ci || npm install

# Préparer les répertoires
echo -e "${YELLOW}Préparation des répertoires...${NC}"
mkdir -p public/build/assets

# Définir des variables d'environnement pour le build
export NODE_OPTIONS="--max-old-space-size=4096"
export NODE_ENV="production"

# Compiler les assets avec Vite
echo -e "${YELLOW}Compilation des assets avec Vite...${NC}"
if npm run build; then
    echo -e "${GREEN}✅ Assets compilés avec succès${NC}"
    
    # Vérifier les fichiers générés
    if [ -f public/build/manifest.json ]; then
        echo -e "${GREEN}✅ manifest.json généré${NC}"
        cat public/build/manifest.json
    else
        echo -e "${RED}❌ manifest.json manquant${NC}"
    fi
    
    # Créer une archive des assets
    echo -e "${YELLOW}Création de l'archive d'assets...${NC}"
    cd public
    tar -czf ../frontend-assets.tar.gz build
    cd ..
    
    echo -e "${GREEN}✅ Archive créée: frontend-assets.tar.gz${NC}"
    echo -e "${YELLOW}Vous pouvez maintenant transférer cette archive sur votre serveur avec:${NC}"
    echo -e "scp frontend-assets.tar.gz utilisateur@serveur:/chemin/destination/"
    echo -e "ssh utilisateur@serveur \"cd /chemin/destination && tar -xzf frontend-assets.tar.gz -C /var/www/public/\""
else
    echo -e "${RED}❌ Erreur lors de la compilation des assets${NC}"
    
    # Créer des assets de secours
    echo -e "${YELLOW}Création des assets de secours...${NC}"
    mkdir -p fallback-assets
    
    # Créer le CSS de secours s'il n'existe pas
    if [ ! -f fallback-assets/placeholder-css.css ]; then
        echo -e "/* Placeholder CSS file when Vite build fails */
body {
  font-family: system-ui, -apple-system, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif;
  line-height: 1.5;
}
.container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem;
}
.build-error {
  background-color: #fef2f2;
  border: 1px solid #f87171;
  color: #b91c1c;
  padding: 1rem;
  margin: 1rem 0;
  border-radius: 0.25rem;
}" > fallback-assets/placeholder-css.css
    fi
    
    # Créer le JS de secours s'il n'existe pas
    if [ ! -f fallback-assets/placeholder-js.js ]; then
        echo -e "/* Placeholder JavaScript file when Vite build fails */
document.addEventListener('DOMContentLoaded', function() {
  console.log('Application running in fallback mode due to Vite build failure');
  const body = document.body;
  const banner = document.createElement('div');
  banner.className = 'build-error container';
  banner.innerHTML = '<h1>Application en mode secours</h1><p>L\\'application fonctionne actuellement en mode dégradé.</p>';
  if (body.firstChild) {
    body.insertBefore(banner, body.firstChild);
  } else {
    body.appendChild(banner);
  }
});" > fallback-assets/placeholder-js.js
    fi
    
    # Créer le manifest.json de secours
    echo -e "{\"resources/css/app.css\":{\"file\":\"assets/app.css\"},\"resources/js/app.jsx\":{\"file\":\"assets/app.js\"}}" > placeholder-manifest.json
    
    # Copier les assets de secours
    mkdir -p public/build/assets
    cp fallback-assets/placeholder-css.css public/build/assets/app.css
    cp fallback-assets/placeholder-js.js public/build/assets/app.js
    cp placeholder-manifest.json public/build/manifest.json
    
    # Créer une archive des assets de secours
    echo -e "${YELLOW}Création de l'archive d'assets de secours...${NC}"
    cd public
    tar -czf ../frontend-assets-fallback.tar.gz build
    cd ..
    
    echo -e "${YELLOW}✅ Archive de secours créée: frontend-assets-fallback.tar.gz${NC}"
    echo -e "${YELLOW}Vous pouvez maintenant transférer cette archive sur votre serveur avec:${NC}"
    echo -e "scp frontend-assets-fallback.tar.gz utilisateur@serveur:/chemin/destination/"
    echo -e "ssh utilisateur@serveur \"cd /chemin/destination && tar -xzf frontend-assets-fallback.tar.gz -C /var/www/public/\""
fi

echo -e "${GREEN}=== Fin du script de compilation des assets ===${NC}" 