#!/bin/bash

# Configuration
APP_DIR="/var/www/votre-domaine.com"
GIT_REPO="votre-repo-github"
GIT_BRANCH="main"

# Couleurs pour les messages
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}Début du déploiement...${NC}"

# Vérifier si le répertoire existe
if [ ! -d "$APP_DIR" ]; then
    echo -e "${RED}Le répertoire $APP_DIR n'existe pas. Création...${NC}"
    sudo mkdir -p $APP_DIR
    sudo chown -R $USER:$USER $APP_DIR
fi

# Aller dans le répertoire de l'application
cd $APP_DIR

# Cloner ou mettre à jour le dépôt
if [ ! -d ".git" ]; then
    echo -e "${GREEN}Clonage du dépôt...${NC}"
    git clone https://github.com/$GIT_REPO.git .
else
    echo -e "${GREEN}Mise à jour du dépôt...${NC}"
    git pull origin $GIT_BRANCH
fi

# Installation des dépendances PHP
echo -e "${GREEN}Installation des dépendances PHP...${NC}"
composer install --no-dev --optimize-autoloader

# Installation des dépendances Node.js
echo -e "${GREEN}Installation des dépendances Node.js...${NC}"
npm install --production

# Compilation des assets
echo -e "${GREEN}Compilation des assets...${NC}"
npm run build

# Configuration des permissions
echo -e "${GREEN}Configuration des permissions...${NC}"
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Optimisation de Laravel
echo -e "${GREEN}Optimisation de Laravel...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Redémarrage de PHP-FPM
echo -e "${GREEN}Redémarrage de PHP-FPM...${NC}"
sudo systemctl restart php8.1-fpm

# Redémarrage de Nginx
echo -e "${GREEN}Redémarrage de Nginx...${NC}"
sudo systemctl restart nginx

echo -e "${GREEN}Déploiement terminé avec succès!${NC}" 