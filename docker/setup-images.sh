#!/bin/bash
set -e

# Création du répertoire pour les images
mkdir -p /var/www/public/imagesAccueil

# Vérification si des images existent dans storage/app/public/imagesAccueil
if [ -d "/var/www/storage/app/public/imagesAccueil" ]; then
    echo "Copie des images depuis storage/app/public/imagesAccueil vers public/imagesAccueil"
    cp -f /var/www/storage/app/public/imagesAccueil/* /var/www/public/imagesAccueil/ || true
fi

# Création de fichiers vides si les images n'existent pas pour satisfaire Vite
for img in imageAccueil1.png Calque_1.svg dernierArrivage.png aPropos.png blog1.png blog2.png; do
    if [ ! -f "/var/www/public/imagesAccueil/$img" ]; then
        echo "Création d'un fichier vide pour $img"
        touch "/var/www/public/imagesAccueil/$img"
    fi
done

# Correction du Welcome.jsx pour utiliser le bon chemin d'images
if [ -f "/var/www/resources/js/Pages/Welcome.jsx" ]; then
    echo "Correction des chemins d'importation dans Welcome.jsx"
    # Utiliser sed pour remplacer uniquement les imports d'images
    sed -i "s|import CardAccueil from '.*imageAccueil1.png';|import CardAccueil from '/imagesAccueil/imageAccueil1.png';|g" /var/www/resources/js/Pages/Welcome.jsx
    sed -i "s|import CalqueLogo from '.*Calque_1.svg';|import CalqueLogo from '/imagesAccueil/Calque_1.svg';|g" /var/www/resources/js/Pages/Welcome.jsx
    sed -i "s|import DernierArrivage from '.*dernierArrivage.png';|import DernierArrivage from '/imagesAccueil/dernierArrivage.png';|g" /var/www/resources/js/Pages/Welcome.jsx
    sed -i "s|import APropos from '.*aPropos.png';|import APropos from '/imagesAccueil/aPropos.png';|g" /var/www/resources/js/Pages/Welcome.jsx
    sed -i "s|import Blog1 from '.*blog1.png';|import Blog1 from '/imagesAccueil/blog1.png';|g" /var/www/resources/js/Pages/Welcome.jsx
    sed -i "s|import Blog2 from '.*blog2.png';|import Blog2 from '/imagesAccueil/blog2.png';|g" /var/www/resources/js/Pages/Welcome.jsx
    
    # Ajouter le gestionnaire d'erreur d'image s'il n'existe pas
    if ! grep -q "handleImageError" /var/www/resources/js/Pages/Welcome.jsx; then
        echo "Ajout du gestionnaire d'erreur d'image"
        sed -i '/^import.*from.*$/a import { handleImageError } from "@/Utils/ImageHelper";' /var/www/resources/js/Pages/Welcome.jsx
    fi
    
    # Assurer que les attributs onError sont présents dans les balises img
    sed -i 's|<img src={CardAccueil}|<img src={CardAccueil} onError={handleImageError}|g' /var/www/resources/js/Pages/Welcome.jsx
fi

# Création du gestionnaire d'erreur d'image s'il n'existe pas
if [ ! -d "/var/www/resources/js/Utils" ]; then
    mkdir -p /var/www/resources/js/Utils
fi

echo "Création du fichier ImageHelper.js"
cat > /var/www/resources/js/Utils/ImageHelper.js << 'EOL'
/**
 * Gestionnaire d'erreur pour les images
 * Remplace l'image par un placeholder en cas d'erreur
 */
export const handleImageError = (e) => {
    console.warn(`Image non trouvée: ${e.target.src}`);
    e.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjUwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KIDxnPgogIDx0aXRsZT5MYXllciAxPC90aXRsZT4KICA8cmVjdCBpZD0ic3ZnXzEiIGhlaWdodD0iNTAwIiB3aWR0aD0iNTAwIiB5PSIwIiB4PSIwIiBzdHJva2Utd2lkdGg9IjAiIHN0cm9rZT0iIzAwMCIgZmlsbD0iI2YwZjBmMCIvPgogIDxsaW5lIHN0cm9rZS1saW5lY2FwPSJ1bmRlZmluZWQiIHN0cm9rZS1saW5lam9pbj0idW5kZWZpbmVkIiBpZD0ic3ZnXzIiIHkyPSI1MDAiIHgyPSI1MDAiIHkxPSIwIiB4MT0iMCIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2U9IiNjY2NjY2MiIGZpbGw9Im5vbmUiLz4KICA8bGluZSBzdHJva2UtbGluZWNhcD0idW5kZWZpbmVkIiBzdHJva2UtbGluZWpvaW49InVuZGVmaW5lZCIgaWQ9InN2Z18zIiB5Mj0iNTAwIiB4Mj0iMCIgeTE9IjAiIHgxPSI1MDAiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlPSIjY2NjY2NjIiBmaWxsPSJub25lIi8+CiAgPHRleHQgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgdGV4dC1hbmNob3I9InN0YXJ0IiBmb250LWZhbWlseT0iSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIyNCIgaWQ9InN2Z180IiB5PSIyNTAiIHg9IjE1MCIgc3Ryb2tlLXdpZHRoPSIwIiBzdHJva2U9IiMwMDAiIGZpbGw9IiM5OTk5OTkiPkltYWdlIG5vbiB0cm91dsOpZTwvdGV4dD4KIDwvZz4KPC9zdmc+';
    e.target.onerror = null; // Évite les boucles infinies
};
EOL

# Configuration nginx pour éviter les erreurs 502
mkdir -p /etc/nginx/conf.d
cat > /etc/nginx/conf.d/timeout.conf << 'EOL'
fastcgi_connect_timeout 300;
fastcgi_send_timeout 300;
fastcgi_read_timeout 300;
fastcgi_buffer_size 32k;
fastcgi_buffers 8 32k;
fastcgi_busy_buffers_size 64k;
fastcgi_temp_file_write_size 64k;
EOL

# Configuration php-fpm pour éviter les timeouts
mkdir -p /usr/local/etc/php-fpm.d
cat > /usr/local/etc/php-fpm.d/www.conf << 'EOL'
[www]
user = www-data
group = www-data
listen = 127.0.0.1:9000
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
request_terminate_timeout = 300s
EOL

# S'assurer que les permissions sont correctes
chown -R www-data:www-data /var/www/public/imagesAccueil
chmod -R 755 /var/www/public/imagesAccueil

echo "Configuration des images terminée" 