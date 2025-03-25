#!/bin/bash
set -e

# Fonction pour les logs (avec timestamps)
log() {
    echo "[$(date +%Y-%m-%d\ %H:%M:%S)] $1"
}

# Vérifier si proc_open est disponible
check_proc_open() {
    log "Vérification de la disponibilité de proc_open..."
    if php -r "echo function_exists('proc_open') ? 'OK' : 'DISABLED';" | grep -q "DISABLED"; then
        log "ERREUR: proc_open est désactivé, ce qui est requis pour Laravel"
        log "Activation de proc_open..."
        echo "disable_functions = " > /usr/local/etc/php/conf.d/docker-php-enable-functions.ini
        log "proc_open a été activé"
    else
        log "proc_open est disponible"
    fi
}

# Fonction pour vérifier la connexion à la base de données (optimisée)
wait_for_db() {
    log "Vérification de la connexion à la base de données..."
    
    # Extraire les variables d'environnement pour la connexion à la BD
    DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
    DB_PORT=$(grep DB_PORT .env | cut -d '=' -f2)
    DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
    DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
    DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)
    
    max_tries=15
    tries=0
    
    # Utiliser une commande PHP simple pour tester la connexion
    while ! php -r "try { \$pdo = new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); echo 'OK'; } catch (\Exception \$e) { exit(1); }" 2>/dev/null; do
        tries=$((tries + 1))
        if [ $tries -gt $max_tries ]; then
            log "AVERTISSEMENT: Impossible de se connecter à la base de données après $max_tries tentatives. Continuons quand même..."
            return
        fi
        log "Tentative $tries/$max_tries - Base de données non disponible, nouvelle tentative dans 2 secondes..."
        sleep 2
    done
    
    log "Connexion à la base de données établie!"
}

# Fonction pour configurer les permissions (simplifiée)
setup_permissions() {
    log "Configuration des permissions..."
    mkdir -p /var/www/storage/app/public
    mkdir -p /var/www/storage/app/public/products
    mkdir -p /var/www/storage/framework/{sessions,views,cache}
    mkdir -p /var/www/storage/logs
    chown -R www-data:www-data /var/www/storage
    chmod -R 775 /var/www/storage
    chown -R www-data:www-data /var/www/bootstrap/cache
    chmod -R 775 /var/www/bootstrap/cache
    chown -R www-data:www-data /var/www/public
    log "Permissions configurées"
}

# Fonction pour gérer le storage link
setup_storage_link() {
    log "Configuration du lien symbolique storage..."
    
    # Supprimer le lien symbolique s'il existe déjà
    if [ -L /var/www/public/storage ]; then
        log "Suppression du lien symbolique existant..."
        rm -f /var/www/public/storage
    fi
    
    # Créer le lien symbolique
    log "Création du lien symbolique..."
    ln -sf /var/www/storage/app/public /var/www/public/storage
    
    # Vérifier que le lien a été créé correctement
    if [ -L /var/www/public/storage ]; then
        log "Lien symbolique créé avec succès"
    else
        log "ERREUR: Échec de la création du lien symbolique"
        # Essai avec la commande Laravel
        php artisan storage:link --force
    fi
}

# Fonction pour exécuter les migrations uniquement si nécessaire
run_migrations() {
    log "Vérification des migrations..."
    
    # D'abord, vérifier si nous pouvons nous connecter à la base de données
    if ! php -r "try { new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); } catch (\Exception \$e) { exit(1); }" 2>/dev/null; then
        log "Base de données non disponible, migrations ignorées"
        return
    fi
    
    # Vérifier si des migrations sont en attente
    if php artisan migrate:status 2>/dev/null | grep -q "down"; then
        log "Migrations trouvées, exécution..."
        php artisan migrate --force
        log "Migrations terminées"
    else
        log "Aucune migration nécessaire"
    fi
}

# Créer des dossiers produits vides si nécessaire
setup_product_folders() {
    log "Configuration des dossiers produits..."
    
    # Créer les dossiers nécessaires
    mkdir -p /var/www/storage/app/public/products
    
    # Définir les permissions
    chown -R www-data:www-data /var/www/storage/app/public
    chmod -R 775 /var/www/storage/app/public
    
    log "Dossiers produits configurés"
}

# Fonction pour créer des images par défaut
setup_default_images() {
    log "Configuration des images par défaut..."
    
    # Créer le dossier pour les images par défaut
    DEFAULT_IMAGES_DIR="/var/www/public/images/default"
    mkdir -p $DEFAULT_IMAGES_DIR
    
    # Vérifier si imagemagick est disponible
    if command -v convert >/dev/null 2>&1; then
        # Créer le favicon par défaut s'il n'existe pas
        if [ ! -f "/var/www/public/favicon.ico" ]; then
            log "Création du favicon par défaut..."
            
            # Créer un favicon en PNG noir (avec imagemagick)
            convert -size 16x16 xc:black $DEFAULT_IMAGES_DIR/favicon.png
            
            # Copier comme favicon.ico à la racine
            cp $DEFAULT_IMAGES_DIR/favicon.png /var/www/public/favicon.ico
            chmod 644 /var/www/public/favicon.ico
        fi
        
        # Générer des images par défaut noires pour différents types
        log "Création des images noires par défaut..."
        
        # Image placeholder noire
        convert -size 500x500 xc:black -fill white -gravity center -pointsize 24 -annotate 0 "Image non disponible" $DEFAULT_IMAGES_DIR/placeholder.png
        
        # Images spécifiques par type
        for type in product category user banner logo thumbnail; do
            convert -size 500x500 xc:black -fill white -gravity center -pointsize 24 -annotate 0 "$type" $DEFAULT_IMAGES_DIR/$type.png
        done
    else
        log "ImageMagick non disponible, création d'images par défaut simples..."
        
        # Créer un fichier favicon.ico simple (16x16 pixels noirs)
        echo -e '\x00\x00\x01\x00\x01\x00\x10\x10\x00\x00\x01\x00\x18\x00h\x03\x00\x00\x16\x00\x00\x00(\x00\x00\x00\x10\x00\x00\x00 \x00\x00\x00\x01\x00\x18\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00' > $DEFAULT_IMAGES_DIR/favicon.ico
        cp $DEFAULT_IMAGES_DIR/favicon.ico /var/www/public/favicon.ico
        
        # Créer un fichier PNG simple pour les placeholders
        touch $DEFAULT_IMAGES_DIR/placeholder.png
        touch $DEFAULT_IMAGES_DIR/product.png
        touch $DEFAULT_IMAGES_DIR/category.png
        touch $DEFAULT_IMAGES_DIR/user.png
        touch $DEFAULT_IMAGES_DIR/banner.png
        touch $DEFAULT_IMAGES_DIR/logo.png
        touch $DEFAULT_IMAGES_DIR/thumbnail.png
    fi
    
    # Définir les permissions
    chown -R www-data:www-data "$DEFAULT_IMAGES_DIR"
    chmod -R 755 "$DEFAULT_IMAGES_DIR"
    chmod 644 /var/www/public/favicon.ico
    chown www-data:www-data /var/www/public/favicon.ico
    
    log "Images par défaut configurées"
}

# Fonction pour corriger le Vite Dev Server
fix_vite_config() {
    log "Correction de la configuration Vite..."
    
    # Désactiver le serveur de développement Vite en production
    if [ -f /var/www/.env ]; then
        log "Suppression de VITE_DEV_SERVER_URL de .env..."
        sed -i '/VITE_DEV_SERVER_URL/d' /var/www/.env
    fi
    
    log "Configuration Vite corrigée"
}

# Fonction principale
main() {
    log "Démarrage de l'application..."
    
    # Vérifier proc_open
    check_proc_open
    
    # Configurer les permissions
    setup_permissions
    
    # Configurer le lien symbolique storage
    setup_storage_link
    
    # Configurer les dossiers produits
    setup_product_folders
    
    # Configurer les images par défaut
    setup_default_images
    
    # Corriger la configuration Vite
    fix_vite_config
    
    # Vérifier la connexion à la base de données (non bloquant)
    wait_for_db
    
    # Exécuter les migrations si nécessaire
    run_migrations
    
    # Vider le cache de config et de vue
    php artisan config:clear
    php artisan view:clear
    
    # Optimisation Laravel
    php artisan optimize
    
    # Démarrage de l'application
    log "Lancement des services..."
    exec "$@"
}

# Exécution du script principal
main "$@"