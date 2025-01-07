import { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import axios from 'axios';

export default function ProductCard({ product }) {
    const [isFavorite, setIsFavorite] = useState(product.isFavorite || false);
    const [isLoading, setIsLoading] = useState(false);

    // Mettre à jour l'état local quand la prop change
    useEffect(() => {
        setIsFavorite(product.isFavorite || false);
    }, [product.isFavorite]);

    const toggleFavorite = async (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (isLoading) return;

        setIsLoading(true);
        try {
            const response = await axios.post(`/products/${product.id}/favorite`);
            setIsFavorite(response.data.isFavorite);
        } catch (error) {
            console.error('Erreur lors de l\'ajout aux favoris:', error);
        } finally {
            setIsLoading(false);
        }
    };

    // S'assurer que les images sont un tableau
    const images = Array.isArray(product.images) ? product.images : JSON.parse(product.images || '[]');
    const imageUrl = product.main_image || (images && images.length > 0
        ? `/storage/products/${images[0]}`
        : '/images/no-image.jpg');

    return (
        <Link href={route('products.show', product.slug)} className="group">
            <div className="relative aspect-square overflow-hidden rounded-lg bg-gray-100">
                <img
                    src={imageUrl}
                    alt={product.name}
                    className="h-full w-full object-cover object-center group-hover:opacity-75"
                />
                <button
                    type="button"
                    onClick={toggleFavorite}
                    disabled={isLoading}
                    className={`absolute top-2 right-2 p-2 rounded-full bg-white shadow-md transition-colors ${
                        isLoading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'
                    }`}
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        className={`h-6 w-6 ${isFavorite ? 'text-red-500' : 'text-gray-400'}`}
                        fill={isFavorite ? 'currentColor' : 'none'}
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                        />
                    </svg>
                </button>
            </div>
            <div className="mt-4 flex justify-between">
                <div>
                    <h3 className="text-sm text-gray-700">{product.name}</h3>
                    <p className="mt-1 text-sm text-gray-500">{product.condition}</p>
                </div>
                <p className="text-sm font-medium text-gray-900">{product.price}€</p>
            </div>
        </Link>
    );
} 