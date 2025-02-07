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

## 🔍 Commandes de Qualité de Code

Nous utilisons des outils de qualité de code pour maintenir des standards élevés dans notre base de code.

### Analyse du Code (PHPStan niveau 5)
```bash
composer analyse
```
Lance une analyse statique approfondie du code pour détecter les erreurs potentielles et les problèmes de typage.

### Vérification du Style
```bash
composer style
```
Vérifie si le code respecte les standards de style sans faire de modifications.

### Correction Automatique du Style
```bash
composer style:fix
```
Corrige automatiquement le style du code selon les standards définis dans la configuration de Laravel Pint.

## 🧪 Tests et Qualité

### Analyse Statique (PHPStan)
```bash
./vendor/bin/phpstan analyse
```

### Style de Code (Laravel Pint)
```bash
./vendor/bin/pint
```

### Tests Unitaires
```bash
php artisan test
```

## 📝 Convention de Commit (Gitmoji)

Pour maintenir une convention cohérente et visuelle, nous utilisons les gitmoji pour nos commits.

# Pour créer un commit avec Gitmoji

Nous utilisons Gitmoji pour maintenir une convention de commits cohérente et visuelle.

## Installation
Vous avez deux options :

### Option 1 : Utilisation locale (sans installation)
Utilisez simplement depuis la racine du projet :
```cmd
.\gitmoji
```

### Option 2 : Installation globale (pour utiliser `gitmoji` partout)
```powershell
# Ouvrir PowerShell en tant qu'administrateur et exécuter :
.\scripts\install-gitmoji.ps1
```
Après l'installation, vous pourrez utiliser :
```cmd
gitmoji
```

Le format des commits est : `<emoji> [CODE] Description`

Types de commits disponibles :

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

## 🤝 Contribution

Les contributions sont les bienvenues ! Voir [CONTRIBUTING.md](CONTRIBUTING.md) pour plus de détails.

## 📄 License

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## Pour lancer le serveur
npm run dev
php artisan serve

## Les commandes utiles
# Pour hasher les passwords des utilisateurs 
php artisan users:update-passwords

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
