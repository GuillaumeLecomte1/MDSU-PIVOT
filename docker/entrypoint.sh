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
    chown -R www-data:www-data /var/www/storage
    chmod -R 775 /var/www/storage
    chown -R www-data:www-data /var/www/bootstrap/cache
    chmod -R 775 /var/www/bootstrap/cache
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

# Fonction pour vérifier ou générer le lien symbolique de stockage
ensure_storage_link() {
    if [ ! -L /var/www/public/storage ]; then
        log "Création du lien symbolique pour le stockage..."
        php artisan storage:link
    fi
}

# Fonction principale
main() {
    log "Démarrage de l'application..."
    
    # Vérifier proc_open
    check_proc_open
    
    # Configurer les permissions
    setup_permissions
    
    # S'assurer que le lien symbolique existe
    ensure_storage_link
    
    # Vérifier la connexion à la base de données (non bloquant)
    wait_for_db
    
    # Exécuter les migrations si nécessaire
    run_migrations
    
    # Démarrage de l'application (sans recréer les caches)
    log "Lancement des services..."
    exec "$@"
}

# Exécution du script principal
main "$@"