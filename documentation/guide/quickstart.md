---
title: Démarrage Rapide
description: Guide de démarrage rapide pour la Marketplace
---

# Démarrage Rapide

Ce guide vous permettra de démarrer rapidement avec la Marketplace.

## Installation rapide

```bash
# Cloner le projet
git clone https://github.com/votre-repo/marketplace.git
cd marketplace

# Installation des dépendances
composer install
npm install

# Configuration
cp .env.example .env
php artisan key:generate

# Base de données
php artisan migrate --seed

# Compilation des assets
npm run build

# Démarrer le serveur
php artisan serve
```

## Configuration minimale

Modifiez le fichier `.env` :

```env
APP_NAME=Marketplace
APP_URL=http://localhost:8000

DB_DATABASE=marketplace
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
```

## Premiers pas

### 1. Créer un compte ressourcerie

1. Accédez à `http://localhost:8000/register`
2. Sélectionnez "Créer un compte ressourcerie"
3. Remplissez le formulaire
4. Validez votre email

### 2. Configurer votre ressourcerie

1. Connectez-vous à votre compte
2. Accédez au dashboard
3. Complétez le profil de votre ressourcerie :
   - Informations générales
   - Localisation
   - Horaires d'ouverture
   - Logo et images

### 3. Ajouter des produits

1. Dans le dashboard, cliquez sur "Produits"
2. Sélectionnez "Ajouter un produit"
3. Remplissez les informations :
   - Nom et description
   - Prix
   - Catégorie
   - Photos
4. Publiez votre produit

## Fonctionnalités essentielles

### 📦 Gestion des produits
- Ajout/modification de produits
- Gestion des stocks
- Catégorisation

### 💳 Paiements
- Configuration Stripe
- Gestion des commandes
- Suivi des transactions

### 📊 Tableau de bord
- Statistiques de vente
- Gestion des commandes
- Notifications

## Prochaines étapes

- [Configuration avancée](./configuration.md)
- [Guide complet des fonctionnalités](../features/)
- [Documentation API](../development/api.md)

## Dépannage rapide

### Problèmes courants

1. **Erreur de base de données**
```bash
php artisan config:clear
php artisan cache:clear
```

2. **Problèmes d'assets**
```bash
npm run dev
```

3. **Erreurs de permission**
```bash
chmod -R 775 storage bootstrap/cache
```

::: tip Conseil
Consultez les logs dans `storage/logs/laravel.log` pour plus de détails sur les erreurs.
:::

::: warning Note
Ce guide rapide ne couvre que les fonctionnalités de base. Pour une utilisation avancée, consultez la documentation complète.
::: 