/**
 * Utilitaire pour gérer les images dans l'application
 */

import { usePage } from '@inertiajs/react';

// Constante pour l'image par défaut
const DEFAULT_IMAGE = '/images/default/placeholder.png';

/**
 * Détermine si une URL est absolue (commence par http:// ou https://)
 * @param {string} url L'URL à vérifier
 * @returns {boolean} True si l'URL est absolue, false sinon
 */
export function isAbsoluteUrl(url) {
    if (!url) return false;
    return url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//');
}

/**
 * Images par défaut pour différentes catégories (en format PNG)
 */
const DEFAULT_IMAGES = {
    product: '/images/default/product.png',
    category: '/images/default/category.png',
    user: '/images/default/user.png',
    banner: '/images/default/banner.png',
    logo: '/images/default/logo.png',
    thumbnail: '/images/default/thumbnail.png',
    fallback: '/images/default/placeholder.png',
};

/**
 * Crée les répertoires pour les images par défaut au démarrage
 */
export function ensureDefaultImages() {
    // Créer les répertoires si nécessaires
    const defaultImagesDir = '/var/www/public/images/default';
    const fs = require('fs');
    if (!fs.existsSync(defaultImagesDir)) {
        try {
            fs.mkdirSync(defaultImagesDir, { recursive: true });
            console.log('Répertoire des images par défaut créé');
        } catch (error) {
            console.error('Erreur lors de la création du répertoire des images par défaut:', error);
        }
    }
}

/**
 * Génère une URL d'image avec la gestion des erreurs et fallbacks
 * @param {string} path Chemin de l'image
 * @param {string} type Type d'image (product, category, user, etc.)
 * @param {string} size Taille de l'image (thumbnail, medium, large)
 * @returns {string} URL de l'image
 */
export function getImageUrl(path, type = 'fallback', size = null) {
    // Si le chemin est vide ou null, retourner l'image par défaut
    if (!path) {
        return DEFAULT_IMAGES[type] || DEFAULT_IMAGES.fallback;
    }

    // Si l'URL est déjà absolue, la retourner telle quelle
    if (isAbsoluteUrl(path)) {
        return path;
    }

    // Gérer les différents types d'images
    if (path.startsWith('/')) {
        // Chemin absolu par rapport à la racine publique
        return path;
    } else if (path.startsWith('storage/')) {
        // Chemin relatif au dossier storage (déjà dans le format correct)
        return `/${path}`;
    } else {
        // Autres chemins - ajouter le préfixe storage/
        return `/storage/${type}s/${path}`;
    }
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
 * @param {string} type - Type d'image (product, category, etc.)
 * @returns {string} - URL complète de l'image
 */
export function getStorageImageUrl(path, type = 'fallback') {
    if (!path) return DEFAULT_IMAGES[type] || DEFAULT_IMAGES.fallback;
    return `/storage/${path}`;
}

/**
 * Vérifie de manière préventive si une image existe
 * @param {string} url - URL de l'image à vérifier
 * @returns {Promise<boolean>} - Promise qui résout à true si l'image existe
 */
export function checkImageExists(url) {
    return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => resolve(true);
        img.onerror = () => resolve(false);
        img.src = url;
    });
}

/**
 * Gestionnaire d'erreur pour les images qui ne peuvent pas être chargées
 * @param {Event} event Événement d'erreur
 * @param {string} type Type d'image (product, category, user, etc.)
 */
export function handleImageError(event, type = 'fallback') {
    const img = event.target;
    
    // Éviter les boucles infinies en vérifiant si l'image est déjà une image par défaut
    if (img.src.includes('/images/default/')) {
        return;
    }
    
    // Remplacer par l'image par défaut appropriée
    img.src = DEFAULT_IMAGES[type] || DEFAULT_IMAGES.fallback;
    
    // Ajouter une classe pour le styling CSS
    img.classList.add('fallback-image');
}

/**
 * Crée un composant image avec gestion d'erreur
 * @param {Object} props Propriétés de l'image
 * @returns {JSX.Element} Élément JSX pour l'image
 */
export function Image({ src, alt, className, type = 'fallback', size = null, ...props }) {
    const imageSrc = getImageUrl(src, type, size);
    
    return (
        <img
            src={imageSrc}
            alt={alt || 'Image'}
            className={`image-component ${className || ''}`}
            onError={(e) => handleImageError(e, type)}
            {...props}
        />
    );
}

/**
 * Précharge toutes les images par défaut pour garantir qu'elles sont disponibles
 * quand elles sont nécessaires
 */
export function preloadDefaultImages() {
    Object.values(DEFAULT_IMAGES).forEach(url => {
        const img = new Image();
        img.src = url;
    });
}

// Précharger toutes les images par défaut au chargement de ce module
if (typeof window !== 'undefined') {
    preloadDefaultImages();
}

// Export par défaut pour compatibilité avec le code existant
export default {
    getImageUrl,
    getStorageImageUrl,
    handleImageError,
    isAbsoluteUrl,
    Image,
    DEFAULT_IMAGES,
    checkImageExists,
    preloadDefaultImages,
}; 