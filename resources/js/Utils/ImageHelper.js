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
    
    try {
        // Try to get asset_url from Inertia props
        const { asset_url } = usePage().props;
        
        // If asset_url is available, use it
        if (asset_url) {
            return `${asset_url}/${cleanPath}`;
        }
    } catch (error) {
        // If usePage() fails (e.g., during SSR), fallback to relative path
        console.warn('Could not get asset_url from Inertia props, using relative path');
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

/**
 * Helper function to handle image loading errors
 * 
 * @param {Event} event - The error event
 * @param {string} fallbackImage - The fallback image to use
 */
export function handleImageError(event, fallbackImage = 'images/placeholder.jpg') {
    event.target.onerror = null; // Prevent infinite loop
    event.target.src = getImageUrl(fallbackImage);
}

export default {
    getImageUrl,
    getStorageImageUrl,
    handleImageError,
}; 