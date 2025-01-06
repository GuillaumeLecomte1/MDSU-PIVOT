import { useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import ProductCard from '@/Components/Products/ProductCard';
import ActionButton from '@/Components/ActionButton';

export default function Show({ product, similarProducts }) {
    const { auth } = usePage().props;
    const [currentImage, setCurrentImage] = useState(0);
    const [isFavorite, setIsFavorite] = useState(product.is_favorite);

    const toggleFavorite = (e) => {
        e.preventDefault();
        e.stopPropagation();

        if (!auth.user) {
            window.location.href = route('login');
            return;
        }

        setIsFavorite(!isFavorite);
    };

    const images = product.images || [];

    return (
        <AppLayout>
            <Head title={product.name} />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Fil d'ariane */}
                <nav className="text-sm mb-8">
                    <ol className="flex items-center">
                        <li>
                            <Link href={route('categories.index')} className="text-gray-500 hover:text-gray-700">
                                Catégories
                            </Link>
                        </li>
                        <li className="mx-2 text-gray-500">/</li>
                        {product.categories.map((category, index) => (
                            <li key={category.id}>
                                <Link 
                                    href={route('categories.show', category.slug)} 
                                    className="text-gray-500 hover:text-gray-700"
                                >
                                    {category.name}
                                </Link>
                                {index < product.categories.length - 1 && <span className="mx-2 text-gray-500">/</span>}
                            </li>
                        ))}
                        <li className="mx-2 text-gray-500">/</li>
                        <li className="text-gray-900">{product.name}</li>
                    </ol>
                </nav>

                <div className="flex flex-col lg:flex-row gap-8">
                    {/* Galerie d'images */}
                    <div className="lg:w-2/3">
                        <div className="relative pb-[100%] bg-gray-100 rounded-lg overflow-hidden">
                            <img
                                src={images[currentImage] ? `/storage/products/${images[currentImage]}` : '/images/no-image.jpg'}
                                alt={product.name}
                                className="absolute top-0 left-0 w-full h-full object-cover"
                            />
                        </div>
                        {images.length > 1 && (
                            <div className="mt-4 grid grid-cols-4 gap-4">
                                {images.map((image, index) => (
                                    <button
                                        key={index}
                                        onClick={() => setCurrentImage(index)}
                                        className={`relative pb-[100%] bg-gray-100 rounded-lg overflow-hidden ${
                                            currentImage === index ? 'ring-2 ring-green-500' : ''
                                        }`}
                                    >
                                        <img
                                            src={`/storage/products/${image}`}
                                            alt={`${product.name} - Image ${index + 1}`}
                                            className="absolute top-0 left-0 w-full h-full object-cover"
                                        />
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Informations produit */}
                    <div className="lg:w-1/3">
                        <div className="sticky top-8">
                            {product.categories.map((category) => (
                                <span key={category.id} className="text-green-500 font-bold mr-2">
                                    {category.name.toUpperCase()}
                                </span>
                            ))}
                            <div className="flex justify-between items-start mt-2">
                                <h1 className="text-2xl font-bold">{product.name}</h1>
                                <ActionButton
                                    route={route('products.favorite', product.id)}
                                    onClick={toggleFavorite}
                                    className="text-gray-400 hover:text-red-500"
                                >
                                    <svg 
                                        className={`w-6 h-6 ${isFavorite ? 'text-red-500 fill-current' : ''}`}
                                        xmlns="http://www.w3.org/2000/svg" 
                                        fill="none" 
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
                                </ActionButton>
                            </div>

                            <div className="mt-4">
                                <Link 
                                    href={route('ressourceries.show', product.ressourcerie.id)}
                                    className="text-gray-500 hover:text-gray-700"
                                >
                                    {product.ressourcerie.name} ({product.ressourcerie.city})
                                </Link>
                            </div>

                            {product.dimensions && (
                                <div className="mt-4">
                                    <h2 className="font-semibold">Dimensions</h2>
                                    <p className="text-gray-600">{product.dimensions}</p>
                                </div>
                            )}

                            <div className="mt-4">
                                <h2 className="font-semibold">Description</h2>
                                <p className="text-gray-600">{product.description}</p>
                            </div>

                            <div className="mt-8">
                                <div className="text-3xl font-bold">{product.price} €</div>
                                <button className="w-full mt-4 bg-black text-white py-3 px-4 rounded-lg hover:bg-gray-800">
                                    Ajouter au panier
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Produits similaires */}
                {similarProducts.length > 0 && (
                    <div className="mt-16">
                        <div className="flex justify-between items-center mb-6">
                            <h2 className="text-xl font-bold">Également dans cette ressourcerie</h2>
                            <Link 
                                href={route('ressourceries.show', product.ressourcerie.id)}
                                className="text-gray-600 hover:text-gray-900"
                            >
                                Voir plus
                            </Link>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            {similarProducts.map((similarProduct) => (
                                <ProductCard key={similarProduct.id} product={similarProduct} />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
} 