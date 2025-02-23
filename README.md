# Pivot

<div align="center">

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%205-brightgreen.svg)](https://phpstan.org/)
[![StyleCI](https://github.styleci.io/repos/872337695/shield?branch=main)](https://github.styleci.io/repos/872337695)
[![Vercel](https://therealsujitk-vercel-badge.vercel.app/?app=pivot)](https://vercel.com/guillaume-lecomtes-projects/pivot)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

</div>

## 📋 À propos

Pivot est une plateforme de commerce en ligne permettant aux ressourceries de vendre leurs produits. Le projet est construit avec Laravel 10, Inertia.js et React, offrant une expérience utilisateur moderne et fluide.

## 🚀 Fonctionnalités

- 🛍️ Gestion des produits et catégories
- 👥 Système d'authentification multi-rôles
- 🏪 Espace ressourcerie
- 🛒 Panier d'achat dynamique
- 💳 Système de paiement sécurisé
- 📱 Interface responsive et moderne
- 🗺️ Intégration de cartes interactives
- 🔔 Notifications en temps réel

## 🛠️ Stack Technique

### Backend
- **Framework:** Laravel 10.x
- **PHP Version:** 8.1+
- **Base de données:** MySQL
- **API:** RESTful API avec Laravel Resources
- **Authentication:** Laravel Sanctum
- **Validation:** Form Requests
- **Cache:** Laravel Cache
- **File Storage:** Laravel Storage

### Frontend
- **Framework:** React 18
- **Server-Side Rendering:** Inertia.js
- **State Management:** Inertia + React Hooks
- **Styling:** Tailwind CSS 3
- **UI Components:**
  - Headless UI
  - React Leaflet (cartes)
  - React Toastify (notifications)
- **Build Tool:** Vite

### DevOps & Qualité
- **Déploiement:** Vercel
- **Analyse Statique:** PHPStan (Level 5)
- **Style de Code:**
  - Laravel Pint
  - StyleCI
- **Convention de Commits:** Gitmoji
- **CI/CD:** Vercel Automatic Deployments

## 📦 Installation

1. Cloner le projet
```bash
git clone https://github.com/votre-repo/marketplace.git
cd marketplace
```

2. Installer les dépendances
```bash
composer install
npm install
```

3. Configurer l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurer la base de données dans `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pivot
DB_USERNAME=pivot
DB_PASSWORD=pivot
```

5. Migrer la base de données
```bash
php artisan migrate --seed
```

6. Lancer le serveur de développement
```bash
npm run dev # Terminal 1
php artisan serve # Terminal 2
```

## 🔍 Scripts de Développement

```bash
# Développement
npm run dev         # Lancer le serveur de développement frontend
php artisan serve   # Lancer le serveur de développement backend

# Production
npm run build      # Builder les assets pour la production
php artisan optimize # Optimiser Laravel pour la production

# Qualité de Code
composer analyse   # Lancer PHPStan
composer style     # Vérifier le style du code
composer style:fix # Corriger automatiquement le style
```

## 📝 Gestion des Commits (Gitmoji)

Nous utilisons Gitmoji pour maintenir une convention de commits cohérente et visuelle.

### Installation de l'alias Gitmoji

Pour faciliter l'utilisation de Gitmoji, vous pouvez configurer un alias dans votre terminal :

```bash
# Pour PowerShell (Windows)
Function gitmoji { npx gitmoji-cli }
Set-Alias -Name gitmoji -Value gitmoji

# Pour Bash/Zsh (Linux/MacOS)
alias gitmoji="npx gitmoji-cli"
```

Pour rendre l'alias permanent :
- **Windows** : Ajoutez la fonction et l'alias dans votre profil PowerShell (`$PROFILE`)
- **Linux/MacOS** : Ajoutez l'alias dans votre `.bashrc` ou `.zshrc`

### Types de Commits Principaux

| Emoji | Code | Description |
|-------|------|-------------|
| ✨ | [FEAT] | Nouvelle fonctionnalité |
| 🐛 | [FIX] | Correction de bug |
| ♻️ | [REFACTOR] | Refactoring du code |
| 🎨 | [STYLE] | Amélioration UI/UX |
| ⚡ | [PERF] | Optimisation des performances |
| 📝 | [DOCS] | Documentation |
| ✅ | [TEST] | Tests |

## 🤝 Contribution

Les contributions sont les bienvenues ! Voir [CONTRIBUTING.md](CONTRIBUTING.md) pour plus de détails.

## 📄 License

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.