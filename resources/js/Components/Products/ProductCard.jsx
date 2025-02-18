import { Link } from '@inertiajs/react';

export default function ProductCard({ product, showEditButton = false, editRoute = null }) {
    const mainImage = product.images && product.images.length > 0
        ? `/storage/${product.images[0].path}`
        : '/images/placeholder.jpg';

    return (
        <div className="bg-white rounded-lg shadow-sm overflow-hidden border hover:shadow-lg transition-shadow duration-200">
            <div className="relative">
                <img
                    src={mainImage}
                    alt={product.name}
                    className="w-full h-48 object-cover"
                />
                {showEditButton && editRoute && (
                    <Link
                        href={route(editRoute, product.id)}
                        className="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 transition-colors"
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
                            {product.categories.map(cat => cat.name).join(', ')}
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
    );
} 