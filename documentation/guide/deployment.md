---
title: Guide de Déploiement
description: Guide détaillé pour déployer l'application Laravel avec Inertia.js sur un VPS utilisant Nginx
head:
  - - meta
    - name: keywords
      content: laravel, inertia, deployment, vps, nginx, php, mysql
---

# Guide de Déploiement

::: tip Prérequis
- VPS sous Ubuntu 22.04 LTS
- Nginx
- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- Composer 2.x
- Git
- Certificat SSL (Let's Encrypt)
:::

## 1. Configuration initiale du serveur

### Connexion SSH et mise à jour
```bash
ssh root@votre_ip
apt update && apt upgrade -y
```

### Installation des dépendances système
```bash
# Ajout des PPA nécessaires
add-apt-repository ppa:ondrej/php
add-apt-repository ppa:ondrej/nginx

# Installation des paquets
apt install -y nginx mysql-server php8.2-fpm php8.2-cli \
    php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring \
    php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl \
    git unzip npm certbot python3-certbot-nginx

# Installation de Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Installation de Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

### Configuration de MySQL
```bash
# Sécurisation de MySQL
mysql_secure_installation

# Création de la base de données
mysql -u root -p
CREATE DATABASE marketplace;
CREATE USER 'marketplace'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
GRANT ALL PRIVILEGES ON marketplace.* TO 'marketplace'@'localhost';
FLUSH PRIVILEGES;
```

## 2. Configuration de Nginx

### Configuration du site
```nginx
# /etc/nginx/sites-available/marketplace
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/marketplace/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Configuration Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Configuration VitePress
    location /docs {
        alias /var/www/marketplace/documentation/.vitepress/dist;
        try_files $uri $uri/ /docs/index.html;
    }

    # Configuration PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Configuration des assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires max;
        log_not_found off;
        access_log off;
    }

    # Désactivation des fichiers cachés
    location ~ /\. {
        deny all;
    }
}
```

### Activation du site
```bash
ln -s /etc/nginx/sites-available/marketplace /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

### Configuration SSL avec Let's Encrypt
```bash
certbot --nginx -d votre-domaine.com
```

## 3. Déploiement de l'application

### Préparation du répertoire
```bash
mkdir -p /var/www/marketplace
chown -R www-data:www-data /var/www/marketplace
```

### Clone et configuration du projet
```bash
cd /var/www/marketplace
git clone votre-repo .
cp .env.example .env
composer install --no-dev --optimize-autoloader

# Configuration de l'environnement
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
php artisan key:generate

# Configuration des permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Configuration de la base de données
```bash
# Éditer le fichier .env avec les bonnes informations
DB_DATABASE=pivot
DB_USERNAME=pivot
DB_PASSWORD=pivot

# Exécution des migrations
php artisan migrate --force
```

## 4. Compilation des assets

### Build de l'application Laravel/Vite
```bash
# Installation des dépendances
npm ci
npm run build

# Configuration du storage
php artisan storage:link
```

### Build de la documentation VitePress
```bash
# Build de la documentation
cd documentation
npm ci
npm run docs:build
```

## 5. Configuration du cache

### Optimisation Laravel
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Configuration du cache Nginx
```nginx
# /etc/nginx/conf.d/cache.conf
fastcgi_cache_path /var/run/nginx-cache levels=1:2 keys_zone=MARKETPLACE:100m inactive=60m;
fastcgi_cache_key "$scheme$request_method$host$request_uri";
fastcgi_cache_use_stale error timeout invalid_header http_500;
fastcgi_cache_valid 200 60m;
```

## 6. Sécurité

### Configuration du pare-feu
```bash
# Installation et configuration de UFW
apt install ufw
ufw allow 22
ufw allow 80
ufw allow 443
ufw enable
```

### Configuration de fail2ban
```bash
apt install fail2ban
cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
systemctl restart fail2ban
```

## 7. Supervision

### Installation de Supervisor
```bash
apt install supervisor

# Configuration des workers Laravel
cat > /etc/supervisor/conf.d/marketplace-worker.conf << EOL
[program:marketplace-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/marketplace/artisan queue:work
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/marketplace/storage/logs/worker.log
EOL

supervisorctl reread
supervisorctl update
supervisorctl start all
```

## 8. Maintenance et mises à jour

### Script de déploiement
```bash
#!/bin/bash
# /var/www/marketplace/deploy.sh

cd /var/www/marketplace

# Pull des changements
git pull origin main

# Installation des dépendances
composer install --no-dev --optimize-autoloader
npm ci

# Build des assets
npm run build
cd documentation && npm run docs:build && cd ..

# Cache et optimisations
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Redémarrage des services
supervisorctl restart all
systemctl restart php8.2-fpm
systemctl restart nginx

echo "Déploiement terminé!"
```

### Automatisation avec crontab
```bash
# Sauvegarde quotidienne
0 2 * * * mysqldump -u marketplace -p marketplace > /var/backups/marketplace-$(date +\%Y\%m\%d).sql

# Nettoyage des vieilles sauvegardes
0 3 * * * find /var/backups/ -name "marketplace-*.sql" -mtime +7 -delete
```

## 9. Monitoring

### Installation de Prometheus et Grafana
```bash
# Installation de Prometheus
apt install prometheus

# Installation de Grafana
apt install grafana
systemctl enable grafana-server
systemctl start grafana-server
```

### Configuration des métriques Laravel
```php
// config/prometheus.php
return [
    'storage_adapter' => 'redis',
    'redis' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', 6379),
        'database' => 3,
    ],
];
```

## 10. Dépannage

### Vérification des logs
```bash
# Logs Nginx
tail -f /var/log/nginx/error.log

# Logs Laravel
tail -f /var/www/marketplace/storage/logs/laravel.log

# Logs PHP-FPM
tail -f /var/log/php8.2-fpm.log
```

### Commandes utiles
```bash
# Vérification du statut des services
systemctl status nginx
systemctl status php8.2-fpm
systemctl status mysql
systemctl status supervisor

# Vérification de la configuration Nginx
nginx -t

# Test de la configuration PHP
php -v
php -m

# Vérification des permissions
ls -la /var/www/marketplace
ls -la /var/www/marketplace/storage
```

::: warning Note importante
- Toujours faire une sauvegarde avant une mise à jour majeure
- Tester les mises à jour sur un environnement de staging
- Maintenir à jour les dépendances de sécurité
:::

::: tip Optimisation des performances
- Utiliser Redis pour le cache et les sessions
- Configurer OPcache pour PHP
- Mettre en place un CDN pour les assets statiques
- Optimiser les images et les assets
::: 