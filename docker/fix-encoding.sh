#!/bin/bash

echo "Checking and fixing file encoding issues..."

# Liste des fichiers à vérifier
FILES_TO_CHECK=(
    "/var/www/Dockerfile"
    "/var/www/docker/nginx.conf"
    "/var/www/docker/supervisord.conf"
    "/var/www/docker/php-fpm.conf"
    "/var/www/docker/check-vite-manifest.sh"
    "/var/www/docker/fix-vite-issues.php"
    "/var/www/docker/healthcheck.sh"
    "/var/www/docker/fix-static-files.sh"
    "/var/www/docker/fix-manifest.sh"
    "/var/www/docker/create-placeholder.sh"
    "/var/www/docker/fix-images.sh"
)

# Vérifier et corriger chaque fichier
for file in "${FILES_TO_CHECK[@]}"; do
    if [ -f "$file" ]; then
        echo "Checking file: $file"
        
        # Créer une copie temporaire sans caractères NUL
        tr -d '\000' < "$file" > "${file}.clean"
        
        # Vérifier si le fichier a été modifié
        if cmp -s "$file" "${file}.clean"; then
            echo "✅ File is clean: $file"
            rm "${file}.clean"
        else
            echo "🔄 Fixing encoding issues in: $file"
            mv "${file}.clean" "$file"
            chmod --reference="${file}" "${file}"
        fi
    else
        echo "⚠️ File not found: $file"
    fi
done

echo "File encoding check completed!" 