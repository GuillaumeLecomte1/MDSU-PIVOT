# Pivot

<div align="center">

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%205-brightgreen.svg)](https://phpstan.org/)
[![StyleCI](https://github.styleci.io/repos/your-repo-id/shield)](https://github.styleci.io/repos/your-repo-id)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

</div>

## 📋 À propos

Pivot est une plateforme de commerce en ligne permettant aux ressourceries de vendre leurs produits. Le projet est construit avec Laravel 10 et utilise les dernières pratiques de développement.

## 🚀 Fonctionnalités

- 🛍️ Gestion des produits et catégories
- 👥 Système d'authentification multi-rôles
- 🏪 Espace ressourcerie
- 🛒 Panier d'achat
- 💳 Système de paiement
- 📱 Interface responsive

## 🛠️ Technologies

- **Framework:** Laravel 10.x
- **PHP Version:** 8.1+
- **Base de données:** MySQL
- **Front-end:** Blade, TailwindCSS
- **Authentication:** Laravel Breeze
- **Qualité de code:**
  - PHPStan (Analyse statique)
  - Laravel Pint (Style de code)
  - Gitmoji (Convention de commits)

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
DB_DATABASE=marketplace
DB_USERNAME=root
DB_PASSWORD=
```

5. Migrer la base de données
```bash
php artisan migrate --seed
```

6. Lancer le serveur de développement
```bash
npm run dev
php artisan serve
```

## 🔍 Qualité du Code

### Analyse Statique (PHPStan niveau 5)
```bash
composer analyse
```

### Vérification du Style
```bash
composer style
```

### Correction Automatique du Style
```bash
composer style:fix
```

## 📝 Gestion des Commits (Gitmoji)

Nous utilisons Gitmoji pour maintenir une convention de commits cohérente et visuelle.

### Installation

#### Option 1 : Utilisation locale (sans installation)
```cmd
.\gitmoji
```

#### Option 2 : Installation globale
```powershell
# Ouvrir PowerShell en tant qu'administrateur
.\scripts\install-gitmoji.ps1
```
Après l'installation, utilisez simplement `gitmoji` depuis n'importe où dans le projet.

### Types de Commits

| Emoji | Code | Description |
|-------|------|-------------|
| 🚧 | [WIP] | Work in Progress |
| ⚡ | [PERF] | Performance improvements |
| ✨ | [FEAT] | New feature |
| 🐛 | [FIX] | Bug fix |
| 🎨 | [STYLE] | UI/Style improvements |
| ♻️ | [REFACTOR] | Code refactoring |
| 🔧 | [CONFIG] | Configuration changes |
| 📝 | [DOCS] | Documentation |
| ✅ | [TEST] | Tests |
| 🔥 | [REMOVE] | Remove code/files |

## 🛠️ Commandes Utiles

### Mise à jour des mots de passe utilisateurs
```bash
php artisan users:update-passwords
```

## 🤝 Contribution

Les contributions sont les bienvenues ! Voir [CONTRIBUTING.md](CONTRIBUTING.md) pour plus de détails.

## 📄 License

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.