---
title: Guide
description: Guide d'utilisation et de développement de l'application Marketplace
---

# Guide d'utilisation

Bienvenue dans la documentation de l'application Marketplace. Ce guide vous aidera à comprendre l'architecture, le développement et le déploiement de l'application.

## Structure du projet

L'application est construite avec :

- **Laravel** - Framework PHP backend
- **Inertia.js** - Middleware pour connecter Laravel et React
- **React** - Framework JavaScript frontend
- **MySQL** - Base de données
- **Nginx** - Serveur web

## Sections principales

- [Guide de déploiement](./deployment.md) - Instructions détaillées pour déployer l'application
- Configuration du serveur
- Maintenance et mises à jour
- Sécurité et bonnes pratiques

## Architecture

L'application suit une architecture moderne utilisant :

- **Backend (Laravel)**
  - MVC pattern
  - API RESTful
  - Eloquent ORM
  - Service Layer

- **Frontend (React + Inertia.js)**
  - Components fonctionnels
  - Hooks React
  - State management
  - Responsive design

## Développement

Pour commencer le développement :

1. Cloner le repository
2. Installer les dépendances
3. Configurer l'environnement
4. Lancer le serveur de développement

```bash
git clone <repository>
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
```

## Contribution

Pour contribuer au projet :

1. Créer une branche pour votre fonctionnalité
2. Développer et tester
3. Soumettre une Pull Request
4. Attendre la review

## Support

En cas de problème :

1. Consulter la documentation
2. Vérifier les issues GitHub
3. Contacter l'équipe de développement

---

::: tip Note
Cette documentation est régulièrement mise à jour. N'hésitez pas à suggérer des améliorations.
::: 