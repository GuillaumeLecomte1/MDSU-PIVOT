---
title: Guide d'Installation
description: Instructions détaillées pour l'installation et la configuration initiale
---

# Guide d'Installation

## Prérequis

Avant de commencer l'installation, assurez-vous d'avoir :

### Environnement de développement
- PHP 8.2 ou supérieur
- Composer 2.x
- Node.js 18.x ou supérieur
- Git
- MySQL 8.0 ou MariaDB 10.5+

### Extensions PHP requises
- php8.2-fpm
- php8.2-mysql
- php8.2-zip
- php8.2-gd
- php8.2-mbstring
- php8.2-curl
- php8.2-xml
- php8.2-bcmath
- php8.2-intl

## Installation en local

### 1. Cloner le repository

```bash
git clone <votre-repo> marketplace
cd marketplace
```

### 2. Installation des dépendances

```bash
# Installation des dépendances PHP
composer install

# Installation des dépendances JavaScript
npm install
```

### 3. Configuration de l'environnement

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate
```

Modifiez le fichier `.env` avec vos paramètres :

```env
APP_NAME=Marketplace
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketplace
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Configuration de la base de données

```bash
# Créer la base de données
mysql -u root -p
```

```sql
CREATE DATABASE marketplace;
CREATE USER 'marketplace'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
GRANT ALL PRIVILEGES ON marketplace.* TO 'marketplace'@'localhost';
FLUSH PRIVILEGES;
```

### 5. Migration et seeding

```bash
# Exécuter les migrations
php artisan migrate

# Remplir la base de données avec des données de test
php artisan db:seed
```

### 6. Configuration du stockage

```bash
# Créer le lien symbolique pour le stockage
php artisan storage:link

# Définir les permissions
chmod -R 775 storage bootstrap/cache
```

### 7. Compilation des assets

```bash
# En développement
npm run dev

# Pour la production
npm run build
```

### 8. Configuration de la documentation

```bash
# Installation des dépendances de la documentation
cd documentation
npm install

# Lancement du serveur de documentation
npm run docs:dev
```

### 9. Lancer l'application

```bash
# Dans un terminal
php artisan serve

# Dans un autre terminal
npm run dev
```

L'application sera accessible aux adresses :
- Application : `http://localhost:8000`
- Documentation : `http://localhost:5173/docs`

## Configuration des services externes

### Stripe (Paiements)

1. Créez un compte sur [Stripe](https://stripe.com)
2. Ajoutez vos clés API dans `.env` :
```env
STRIPE_KEY=votre_cle_publique
STRIPE_SECRET=votre_cle_secrete
```

### Géolocalisation

1. Créez un compte sur [MapBox](https://www.mapbox.com/)
2. Ajoutez votre clé API dans `.env` :
```env
MAPBOX_TOKEN=votre_token
```

## Dépannage

### Problèmes courants

1. **Erreur de permissions**
```bash
# Réinitialiser les permissions
sudo chown -R $USER:www-data .
sudo find . -type f -exec chmod 664 {} \;
sudo find . -type d -exec chmod 775 {} \;
```

2. **Erreur de composer**
```bash
composer dump-autoload
php artisan clear-compiled
composer install
```

3. **Erreur de cache**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

4. **Erreur de Node.js**
```bash
rm -rf node_modules
rm package-lock.json
npm cache clean --force
npm install
```

::: tip Conseil
Consultez les logs dans `storage/logs/laravel.log` pour plus de détails sur les erreurs.
:::

::: warning Note
En cas de problème avec la documentation, vérifiez les logs de VitePress dans `documentation/.vitepress/logs/`.
:::

## Environnement de développement recommandé

### IDE
- Visual Studio Code avec les extensions :
  - Laravel Extension Pack
  - Volar (Vue)
  - PHP Intelephense
  - Tailwind CSS IntelliSense

### Outils de développement
- Laravel Debugbar
- Laravel IDE Helper
- Laravel Telescope

### Outils de test
- PHPUnit
- Laravel Dusk
- Pest PHP

::: tip Productivité
Utilisez [Laravel Sail](https://laravel.com/docs/sail) pour un environnement Docker préconfiguré.
::: 