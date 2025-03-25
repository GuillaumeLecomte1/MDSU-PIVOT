#!/bin/bash
set -e

echo "=== Vérification des problèmes d'importation dans les composants React ==="

# Vérification et correction des problèmes connus

# 1. Problème d'export dans ImageHelper.js
if [ -f "/var/www/resources/js/Utils/ImageHelper.js" ]; then
    echo "Vérification des exports dans ImageHelper.js"
    
    # Vérifier si isAbsoluteUrl est exporté correctement
    if ! grep -q "export function isAbsoluteUrl" /var/www/resources/js/Utils/ImageHelper.js; then
        echo "Export manquant pour isAbsoluteUrl, ajout..."
        # Ajouter l'export manquant juste après les imports
        sed -i '/^import/a\
export function isAbsoluteUrl(url) {\
    if (!url) return false;\
    return url.startsWith("http://") || url.startsWith("https://") || url.startsWith("//");\
}' /var/www/resources/js/Utils/ImageHelper.js
    fi
    
    # Vérifier si getImageUrl est exporté correctement
    if ! grep -q "export function getImageUrl" /var/www/resources/js/Utils/ImageHelper.js; then
        echo "Export manquant pour getImageUrl, ajout..."
        sed -i '/^import/a\
export function getImageUrl(path) {\
    if (!path) return "/images/placeholder.jpg";\
    if (isAbsoluteUrl(path)) return path;\
    return `/${path.startsWith("/") ? path.substring(1) : path}`;\
}' /var/www/resources/js/Utils/ImageHelper.js
    fi
    
    # Vérifier si handleImageError est exporté correctement
    if ! grep -q "export const handleImageError" /var/www/resources/js/Utils/ImageHelper.js; then
        echo "Export manquant pour handleImageError, ajout..."
        cat >> /var/www/resources/js/Utils/ImageHelper.js << 'EOL'
export const handleImageError = (e) => {
    console.warn(`Image non trouvée: ${e.target.src}`);
    e.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjUwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KIDxnPgogIDx0aXRsZT5MYXllciAxPC90aXRsZT4KICA8cmVjdCBpZD0ic3ZnXzEiIGhlaWdodD0iNTAwIiB3aWR0aD0iNTAwIiB5PSIwIiB4PSIwIiBzdHJva2Utd2lkdGg9IjAiIHN0cm9rZT0iIzAwMCIgZmlsbD0iI2YwZjBmMCIvPgogIDxsaW5lIHN0cm9rZS1saW5lY2FwPSJ1bmRlZmluZWQiIHN0cm9rZS1saW5lam9pbj0idW5kZWZpbmVkIiBpZD0ic3ZnXzIiIHkyPSI1MDAiIHgyPSI1MDAiIHkxPSIwIiB4MT0iMCIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2U9IiNjY2NjY2MiIGZpbGw9Im5vbmUiLz4KICA8bGluZSBzdHJva2UtbGluZWNhcD0idW5kZWZpbmVkIiBzdHJva2UtbGluZWpvaW49InVuZGVmaW5lZCIgaWQ9InN2Z18zIiB5Mj0iNTAwIiB4Mj0iMCIgeTE9IjAiIHgxPSI1MDAiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlPSIjY2NjY2NjIiBmaWxsPSJub25lIi8+CiAgPHRleHQgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgdGV4dC1hbmNob3I9InN0YXJ0IiBmb250LWZhbWlseT0iSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIyNCIgaWQ9InN2Z180IiB5PSIyNTAiIHg9IjE1MCIgc3Ryb2tlLXdpZHRoPSIwIiBzdHJva2U9IiMwMDAiIGZpbGw9IiM5OTk5OTkiPkltYWdlIG5vbiB0cm91dsOpZTwvdGV4dD4KIDwvZz4KPC9zdmc+';
    e.target.onerror = null;
};
EOL
    fi
fi

# 2. Vérification des imports dans les composants qui utilisent ImageHelper.js
for component in $(find /var/www/resources/js/Components -name "*.jsx"); do
    if grep -q "isAbsoluteUrl.*from.*ImageHelper" "$component"; then
        echo "Composant $component utilise isAbsoluteUrl"
        # Vérifier si le composant utilise déjà une définition locale
        if ! grep -q "const isAbsoluteUrl.*=" "$component"; then
            echo "Ajout d'une définition locale dans $component"
            # Remplacer l'import
            sed -i 's/import {.*isAbsoluteUrl.*} from/import {/g' "$component"
            sed -i 's/, isAbsoluteUrl//' "$component"
            sed -i 's/isAbsoluteUrl, //' "$component"
            # Ajouter la définition locale
            sed -i '/^import.*from.*ImageHelper/a\
// Fonction locale pour éviter les problèmes d'\''importation\
const isAbsoluteUrl = (url) => {\
    if (!url) return false;\
    return url.startsWith("http://") || url.startsWith("https://") || url.startsWith("//");\
};' "$component"
        fi
    fi
done

echo "=== Vérification terminée ===" 