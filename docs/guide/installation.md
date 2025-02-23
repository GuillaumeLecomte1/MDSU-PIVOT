---
title: Guide d'Installation
description: Instructions détaillées pour l'installation et la configuration initiale
---

# Guide d'Installation

## Prérequis

Avant de commencer l'installation, assurez-vous d'avoir :

- PHP 8.1 ou supérieur
- Composer
- Node.js et NPM
- Git
- MySQL ou MariaDB

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

### 4. Configuration de la base de données

1. Créez une base de données MySQL
2. Modifiez le fichier `.env` avec vos informations de connexion :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketplace
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

### 5. Migration et seeding

```bash
# Exécuter les migrations
php artisan migrate

# Optionnel : Remplir la base de données avec des données de test
php artisan db:seed
```

### 6. Compilation des assets

```bash
# En développement
npm run dev

# Pour la production
npm run build
```

### 7. Lancer l'application

```bash
php artisan serve
```

L'application sera accessible à l'adresse : `http://localhost:8000`

## Configuration supplémentaire

### Storage Link

Créez le lien symbolique pour le stockage :

```bash
php artisan storage:link
```

### Configuration du mail

Modifiez le fichier `.env` pour configurer l'envoi d'emails :

```env
MAIL_MAILER=smtp
MAIL_HOST=votre-smtp-server
MAIL_PORT=587
MAIL_USERNAME=votre-username
MAIL_PASSWORD=votre-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Dépannage

### Problèmes courants

1. **Erreur de permissions** :
```bash
chmod -R 775 storage bootstrap/cache
```

2. **Erreur de composer** :
```bash
composer dump-autoload
```

3. **Erreur de cache** :
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

::: tip Note
En cas de problème, vérifiez les logs dans `storage/logs/laravel.log`
::: 