import { usePage } from '@inertiajs/react';

/**
 * Helper function to get the correct image URL in both development and production environments
 * 
 * @param {string} path - The path to the image relative to the public directory
 * @returns {string} - The correct URL to the image
 */
export function getImageUrl(path) {
    // Remove leading slash if present
    const cleanPath = path.startsWith('/') ? path.substring(1) : path;
    
    // In development, use the direct path
    if (process.env.NODE_ENV === 'development') {
        return `/${cleanPath}`;
    }
    
    // In production, try to use the asset URL from Inertia props if available
    try {
        const { asset_url } = usePage().props;
        if (asset_url) {
            return `${asset_url}/${cleanPath}`;
        }
    } catch (error) {
        console.warn('Could not get asset_url from Inertia props:', error);
    }
    
    // Fallback to direct path
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
 * Helper function to get a URL for a storage image
 * 
 * @param {string} path - The path to the image relative to the storage directory
 * @returns {string} - The correct URL to the storage image
 */
export function getStorageImageUrl(path) {
    return getImageUrl(`storage/${path}`);
}

export default {
    getImageUrl,
    getStorageImageUrl,
}; 