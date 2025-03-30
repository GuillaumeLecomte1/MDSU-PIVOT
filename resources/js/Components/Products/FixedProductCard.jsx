import { Link, router } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';

// Array of fixed product image colors
const productColors = [
    '#4ADE80', // Green
    '#FF8D7E', // Red
    '#FFD166', // Yellow
    '#118AB2', // Blue
    '#073B4C', // Dark Blue
    '#9381FF'  // Purple
];

// Fonction simple pour gérer les erreurs d'images
const handleImageError = (e) => {
    e.target.src = '/storage/default/placeholder.png';
};

export default function FixedProductCard({ product, index = 0, showEditButton = false, editRoute = null, inFavoritesPage = false }) {
    const { auth } = usePage().props;
    const [isFavorite, setIsFavorite] = useState(inFavoritesPage ? true : product.isFavorite);

    // Mettre à jour l'état si la prop product.isFavorite change
    useEffect(() => {
        if (!inFavoritesPage) {
            setIsFavorite(product.isFavorite);
        }
    }, [product.isFavorite, inFavoritesPage]);

    const [isLoading, setIsLoading] = useState(false);

    const toggleFavorite = (e) => {
        e.preventDefault();
        e.stopPropagation();

        if (!auth.user || isLoading) return;

        setIsLoading(true);
        const newFavoriteState = !isFavorite;
        setIsFavorite(newFavoriteState); // Optimistic update

        router.post(route('favorites.toggle', product.id), {}, {
            preserveScroll: true,
            onSuccess: (page) => {
                const flashData = page.props.flash;
                if (flashData && typeof flashData.isFavorite !== 'undefined') {
                    setIsFavorite(flashData.isFavorite);
                }
            },
            onError: () => {
                setIsFavorite(!newFavoriteState);
                console.error('Erreur lors de la modification des favoris');
            },
            onFinish: () => setIsLoading(false),
        });
    };

    // Get a color for the product image based on the index
    const getProductColor = () => {
        return productColors[index % productColors.length];
    };

    // Generate inline SVG for product image
    const generateProductImageSvg = () => {
        const color = getProductColor();
        const productName = product.name || 'Produit';
        const firstLetter = productName.charAt(0).toUpperCase();
        
        return `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600'%3E%3Crect width='800' height='600' fill='${color.replace('#', '%23')}'/%3E%3Ctext x='400' y='300' font-family='Arial' font-size='120' text-anchor='middle' fill='white' dominant-baseline='middle'%3E${firstLetter}%3C/text%3E%3C/svg%3E`;
    };

    return (
        <Link
            href={route('products.show', product.slug)}
            className="block relative"
        >
            <div className="bg-white rounded-lg shadow-sm overflow-hidden border hover:shadow-lg transition-shadow duration-200">
                <div className="relative">
                    <img
                        src={generateProductImageSvg()}
                        alt={product.name}
                        className="w-full h-48 object-cover rounded-t-lg"
                        onError={handleImageError}
                    />
                    {auth.user && (
                        <button
                            onClick={toggleFavorite}
                            disabled={isLoading}
                            className={`absolute top-2 right-2 p-2 rounded-full bg-white/80 backdrop-blur-sm
                                hover:bg-white transition-all duration-200 ${isLoading ? 'opacity-50 cursor-not-allowed' : ''}`}
                        >
                            <svg 
                                className={`h-6 w-6 transition-colors duration-200 ${
                                    isFavorite 
                                        ? 'text-red-500 fill-current' 
                                        : 'text-gray-400 hover:text-red-500'
                                }`}
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path 
                                    strokeLinecap="round" 
                                    strokeLinejoin="round" 
                                    strokeWidth="2" 
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" 
                                />
                            </svg>
                        </button>
                    )}
                    {showEditButton && editRoute && (
                        <Link
                            href={route(editRoute, product.id)}
                            className="absolute top-2 left-2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 transition-colors"
                            onClick={(e) => e.stopPropagation()}
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </Link>
                    )}
                </div>
                <div className="p-4">
                    <div className="flex justify-between items-start mb-2">
                        <div>
                            <h3 className="text-lg font-semibold text-gray-900">{product.name}</h3>
                            <p className="text-sm text-gray-600">
                                {product.categories && product.categories.map(cat => cat.name).join(', ')}
                            </p>
                        </div>
                        <p className="text-lg font-bold text-emerald-600">{product.price}€</p>
                    </div>
                    <div className="mt-2">
                        <p className="text-sm text-gray-600 line-clamp-2">{product.description}</p>
                    </div>
                    <div className="flex justify-between items-center mt-4">
                        <span className={`px-2 py-1 text-xs font-semibold rounded-full ${
                            product.is_available
                                ? 'bg-green-100 text-green-800'
                                : 'bg-red-100 text-red-800'
                        }`}>
                            {product.is_available ? 'Disponible' : 'Indisponible'}
                        </span>
                        <span className="text-sm text-gray-600">
                            Stock: {product.stock}
                        </span>
                    </div>
                </div>
            </div>
        </Link>
    );
} 