/**
 * Utilitaire pour gérer les images dans l'application
 */

import { usePage } from '@inertiajs/react';

/**
 * Obtient l'URL correcte pour une image
 * @param {string} path - Chemin de l'image
 * @returns {string} - URL complète de l'image
 */
export function getImageUrl(path) {
    // Si le chemin est vide, retourner l'image par défaut
    if (!path) return '/images/placeholder.jpg';
    
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
    if (!path) return '/images/placeholder.jpg';
    return getImageUrl(`storage/${path}`);
}

/**
 * Gère les erreurs de chargement d'image
 * @param {Event} event - Événement d'erreur
 */
export function handleImageError(event) {
    try {
        // Essayer d'obtenir l'URL de base depuis les props Inertia
        const { asset_url } = usePage().props;
        if (asset_url) {
            const baseUrl = asset_url.endsWith('/') ? asset_url.slice(0, -1) : asset_url;
            event.target.src = `${baseUrl}/images/placeholder.jpg`;
        } else {
            event.target.src = '/images/placeholder.jpg';
        }
    } catch (error) {
        // Fallback à une URL relative en cas d'erreur
        event.target.src = '/images/placeholder.jpg';
    }
    
    event.target.onerror = null; // Évite les boucles infinies
}

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