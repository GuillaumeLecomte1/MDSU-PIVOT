#!/bin/sh
set -e

echo "🚀 Démarrage de l'application MDSU-PIVOT..."

cd /var/www

# Création d'une page d'erreur 500 statique
echo "📄 Création de la page d'erreur 500 statique"
cat > public/500.html << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <title>Service temporairement indisponible</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
            color: #333; 
            line-height: 1.6; 
            margin: 0; 
            padding: 0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background-color: #f8f9fa; 
        }
        .container { 
            text-align: center; 
            max-width: 600px; 
            padding: 20px; 
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background-color: white;
        }
        h1 { 
            font-size: 2.5em; 
            margin-bottom: 16px; 
            color: #e53935; 
        }
        p { 
            font-size: 1.1em; 
            margin-bottom: 24px; 
        }
        a { 
            color: #3490dc; 
            text-decoration: none; 
        }
        a:hover { 
            text-decoration: underline; 
        }
        .btn { 
            display: inline-block; 
            background: #3490dc; 
            color: white; 
            padding: 10px 20px; 
            border-radius: 4px; 
            font-weight: bold; 
            transition: background 0.3s; 
        }
        .btn:hover { 
            background: #2779bd; 
            text-decoration: none; 
        }
        .error-code {
            color: #999;
            margin-top: 20px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Service temporairement indisponible</h1>
        <p>Le service est momentanément indisponible. Nous travaillons activement pour résoudre ce problème.</p>
        <p>Veuillez réessayer dans quelques instants.</p>
        <a href="/" class="btn">Retour à l'accueil</a>
        <div class="error-code">Erreur 500</div>
    </div>
</body>
</html>
EOF

# Vérifier si le fichier .env existe, sinon copier .env.example
if [ ! -f .env ]; then
    echo "📝 Création du fichier .env à partir de .env.example"
    cp .env.example .env
    
    # Générer une clé d'application si nécessaire
    echo "🔑 Génération de la clé d'application"
    php artisan key:generate --force || true
else
    echo "✅ Fichier .env trouvé"
fi

# Vérifier et corriger le manifeste Vite
echo "🔍 Vérification du manifeste Vite et des assets frontend..."
if [ ! -f public/build/manifest.json ] || [ $(cat public/build/manifest.json | grep -c "src") -eq 0 ]; then
    echo "⚠️ Manifeste Vite incomplet ou invalide, création d'un manifeste complet"
    mkdir -p public/build/assets
    cat > public/build/manifest.json << 'EOF'
{
    "resources/css/app.css": {
        "file": "assets/app.css",
        "src": "resources/css/app.css",
        "isEntry": true
    },
    "resources/js/app.jsx": {
        "file": "assets/app.js",
        "src": "resources/js/app.jsx",
        "isEntry": true
    }
}
EOF
    
    # Créer des fichiers CSS et JS de secours si nécessaires
    if [ ! -s public/build/assets/app.css ]; then
        echo "/* Fallback CSS */" > public/build/assets/app.css
    fi
    
    if [ ! -s public/build/assets/app.js ]; then
        echo "/* Fallback JS */" > public/build/assets/app.js
    fi
    
    # Modification directe du template blade pour éviter des erreurs avec la directive @vite
    sed -i 's/@vite(\[.*\])/<script src="{{ asset(\x27build\/assets\/app.js\x27) }}"><\/script><link rel="stylesheet" href="{{ asset(\x27build\/assets\/app.css\x27) }}">/' resources/views/app.blade.php
else
    echo "✅ Manifeste Vite valide trouvé"
fi

# Créer le lien symbolique pour le stockage s'il n'existe pas
if [ ! -L public/storage ]; then
    echo "🔗 Création du lien symbolique pour le stockage"
    php artisan storage:link --force || true
else
    echo "✅ Lien symbolique storage trouvé"
fi

# Attendre que la base de données MySQL soit disponible
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "⏳ Attente de la disponibilité de la base de données MySQL..."
    
    ATTEMPTS=0
    MAX_ATTEMPTS=10
    
    while [ $ATTEMPTS -lt $MAX_ATTEMPTS ]; do
        if mysql -h ${DB_HOST:-mysql} -u ${DB_USERNAME:-laravel} -p${DB_PASSWORD:-laravel} -e "SELECT 1" >/dev/null 2>&1; then
            echo "✅ Base de données MySQL disponible"
            break
        fi
        
        ATTEMPTS=$((ATTEMPTS + 1))
        echo "⏳ Tentative $ATTEMPTS/$MAX_ATTEMPTS : Attente de MySQL..."
        sleep 2
        
        if [ $ATTEMPTS -eq $MAX_ATTEMPTS ]; then
            echo "❌ Impossible de se connecter à MySQL après $MAX_ATTEMPTS tentatives"
            echo "⚠️ Continuation sans attendre MySQL..."
        fi
    done
fi

# Nettoyer tous les caches d'abord
echo "🧹 Nettoyage des caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

# Migrations de base de données (avec gestion des erreurs)
echo "🗄️ Exécution des migrations de base de données"
php artisan migrate --force || echo "⚠️ Échec des migrations, continuation..."

# Optimisations avec gestion d'erreur
echo "⚡ Optimisation de l'application pour la production"
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan optimize || true

# Définir les permissions
echo "🔒 Définition des permissions"
find storage bootstrap/cache -type d -exec chmod 775 {} \; || true
find storage bootstrap/cache -type f -exec chmod 664 {} \; || true
chown -R www-data:www-data storage bootstrap/cache || true

# Vérifier une dernière fois que les vues compilées sont correctes
echo "🔍 Vérification finale des vues compilées..."
if grep -q "Vite" storage/framework/views/*.php 2>/dev/null; then
    echo "⚠️ Références Vite trouvées dans les vues compilées, nettoyage..."
    php artisan view:clear || true
    
    # Remplacer directement la directive @vite dans toutes les vues
    find resources/views -type f -name "*.blade.php" -exec sed -i 's/@vite(\[.*\])/<script src="{{ asset(\x27build\/assets\/app.js\x27) }}"><\/script><link rel="stylesheet" href="{{ asset(\x27build\/assets\/app.css\x27) }}">/' {} \;
    
    # Reconstruire le cache des vues
    php artisan view:cache || true
else
    echo "✅ Pas de références problématiques à Vite dans les vues compilées"
fi

# Démarrer supervisor pour gérer les processus
echo "🚦 Démarrage des services (nginx, php-fpm, queue)"
exec supervisord -c /etc/supervisord.conf 