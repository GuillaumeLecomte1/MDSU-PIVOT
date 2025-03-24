# Configuration Docker pour l'application Pivot

Ce dossier contient tous les fichiers nécessaires pour déployer l'application Laravel avec Inertia.js et React en utilisant Docker.

## Fichiers principaux

- `Dockerfile` : Configuration multi-étape pour construire l'image Docker optimisée
- `entrypoint.sh` : Script d'initialisation exécuté au démarrage du conteneur
- `apache.conf` : Configuration du serveur web Apache
- `php.ini` : Configuration PHP optimisée
- `fix-assets.php` : Script de correction des assets pour résoudre les problèmes 404

## Comment ça fonctionne

1. La construction de l'image se fait en deux étapes:
   - Une étape de build où les dépendances sont installées et les assets compilés
   - Une étape finale avec uniquement les fichiers nécessaires pour la production

2. Au démarrage du conteneur, le script `entrypoint.sh` exécute:
   - Génération de la clé d'application
   - Optimisations Laravel pour la production
   - Création des liens symboliques pour le stockage
   - Corrections des permissions
   - Exécution du script `fix-assets.php` pour générer les assets manquants
   - Migrations de base de données (si configurées)

3. Le script `fix-assets.php` garantit que tous les assets nécessaires sont présents:
   - Création des répertoires `build/assets` et `assets`
   - Génération de fichiers JavaScript et CSS de secours
   - Création de versions non-hashées des fichiers assets
   - Configuration des fichiers `.htaccess` pour la redirection des assets

## Comment déployer

1. Construisez l'image Docker:
   ```
   docker build -t pivot -f docker/Dockerfile .
   ```

2. Exécutez le conteneur:
   ```
   docker run -p 8000:80 -e DB_HOST=your_db_host -e DB_USER=user -e DB_PASSWORD=password -e DB_DATABASE=database pivot
   ```

## Variables d'environnement

- `APP_ENV` : Environnement d'application (production, development, testing)
- `APP_DEBUG` : Activer le débogage (true/false)
- `DB_HOST` : Hôte de la base de données
- `DB_PORT` : Port de la base de données
- `DB_DATABASE` : Nom de la base de données
- `DB_USERNAME` : Nom d'utilisateur de la base de données
- `DB_PASSWORD` : Mot de passe de la base de données

## Résolution des problèmes courants

### Assets 404
Si vous rencontrez des erreurs 404 pour les assets CSS/JS, accédez à `/fix-assets.php` dans votre navigateur. Ce script générera des fichiers de secours et les configurations nécessaires.

### Erreurs de base de données
Assurez-vous que les variables d'environnement pour la base de données sont correctement définies et que la base de données est accessible depuis le conteneur.

### Problèmes de permissions
Si vous rencontrez des problèmes de permissions, connectez-vous au conteneur et exécutez:
```
chmod -R 755 /var/www/storage
chown -R www-data:www-data /var/www
```

### Vérification de l'état
Un fichier de diagnostic est disponible à `/server-info.php` pour vérifier l'état du serveur. 