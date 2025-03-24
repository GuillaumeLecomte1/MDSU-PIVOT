/**
 * Utilitaire pour gérer les images dans l'application
 */

import { usePage } from '@inertiajs/react';

// Constante pour l'image par défaut
const DEFAULT_IMAGE = '/images/placeholder.jpg';

/**
 * Obtient l'URL correcte pour une image
 * @param {string} path - Chemin de l'image
 * @returns {string} - URL complète de l'image
 */
export function getImageUrl(path) {
    // Si le chemin est vide, retourner l'image par défaut
    if (!path) return DEFAULT_IMAGE;
    
    // Si c'est déjà une URL absolue, la retourner telle quelle
    if (isAbsoluteUrl(path)) return path;
    
    // Supprimer le slash initial si présent
    const cleanPath = path.startsWith('/') ? path.substring(1) : path;
    
    try {
        // Essayer d'obtenir l'URL de base depuis les props Inertia
        const { asset_url } = usePage().props;
        if (asset_url) {
            // Éviter les doubles slashes
            const baseUrl = asset_url.endsWith('/') ? asset_url.slice(0, -1) : asset_url;
            return `${baseUrl}/${cleanPath}`;
        }
    } catch (error) {
        console.log('Could not get asset_url from Inertia props, using relative path');
    }
    
    // Fallback à une URL relative
    return `/${cleanPath}`;
}

/**
 * Helper function to import images directly in components
 * This is the preferred method as it allows Vite to process and hash the images
 * 
 * @example
 * // Import the image at the top of your component
 * import myImage from '../../public/images/my-image.png';
 * 
 * // Then use it in your component
 * <img src={myImage} alt="My Image" />
 */

/**
 * Obtient l'URL pour une image stockée dans le répertoire storage
 * @param {string} path - Chemin de l'image dans storage
 * @returns {string} - URL complète de l'image
 */
export function getStorageImageUrl(path) {
    if (!path) return DEFAULT_IMAGE;
    return getImageUrl(`storage/${path}`);
}

/**
 * Gestionnaire d'erreur pour les images
 * Remplace l'image par un placeholder en cas d'erreur
 */
export const handleImageError = (e) => {
    console.warn(`Image non trouvée: ${e.target.src}`);
    e.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjUwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KIDxnPgogIDx0aXRsZT5MYXllciAxPC90aXRsZT4KICA8cmVjdCBpZD0ic3ZnXzEiIGhlaWdodD0iNTAwIiB3aWR0aD0iNTAwIiB5PSIwIiB4PSIwIiBzdHJva2Utd2lkdGg9IjAiIHN0cm9rZT0iIzAwMCIgZmlsbD0iI2YwZjBmMCIvPgogIDxsaW5lIHN0cm9rZS1saW5lY2FwPSJ1bmRlZmluZWQiIHN0cm9rZS1saW5lam9pbj0idW5kZWZpbmVkIiBpZD0ic3ZnXzIiIHkyPSI1MDAiIHgyPSI1MDAiIHkxPSIwIiB4MT0iMCIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2U9IiNjY2NjY2MiIGZpbGw9Im5vbmUiLz4KICA8bGluZSBzdHJva2UtbGluZWNhcD0idW5kZWZpbmVkIiBzdHJva2UtbGluZWpvaW49InVuZGVmaW5lZCIgaWQ9InN2Z18zIiB5Mj0iNTAwIiB4Mj0iMCIgeTE9IjAiIHgxPSI1MDAiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlPSIjY2NjY2NjIiBmaWxsPSJub25lIi8+CiAgPHRleHQgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgdGV4dC1hbmNob3I9InN0YXJ0IiBmb250LWZhbWlseT0iSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIyNCIgaWQ9InN2Z180IiB5PSIyNTAiIHg9IjE1MCIgc3Ryb2tlLXdpZHRoPSIwIiBzdHJva2U9IiMwMDAiIGZpbGw9IiM5OTk5OTkiPkltYWdlIG5vbiB0cm91dsOpZTwvdGV4dD4KIDwvZz4KPC9zdmc+';
    e.target.onerror = null; // Évite les boucles infinies
};

/**
 * Vérifie si une URL est absolue
 * @param {string} url - URL à vérifier
 * @returns {boolean} - True si l'URL est absolue
 */
export function isAbsoluteUrl(url) {
    if (!url) return false;
    return url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//');
}

export default {
    getImageUrl,
    getStorageImageUrl,
    handleImageError,
    isAbsoluteUrl
}; 