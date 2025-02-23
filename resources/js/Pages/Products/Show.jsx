import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function Show({ product, similarProducts }) {
    const [selectedImage, setSelectedImage] = useState(0);

    return (
        <MainLayout>
            <Head title={product.name} />
            
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {/* Galerie d'images */}
                    <div className="space-y-4">
                        <div className="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg">
                            <img
                                src={product.images[selectedImage]}
                                alt={product.name}
                                className="h-full w-full object-cover object-center"
                            />
                        </div>
                        <div className="grid grid-cols-4 gap-4">
                            {product.images.map((image, index) => (
                                <button
                                    key={index}
                                    onClick={() => setSelectedImage(index)}
                                    className={`aspect-w-1 aspect-h-1 overflow-hidden rounded-lg ${
                                        selectedImage === index ? 'ring-2 ring-green-500' : ''
                                    }`}
                                >
                                    <img
                                        src={image}
                                        alt={`${product.name} ${index + 1}`}
                                        className="h-full w-full object-cover object-center"
                                    />
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* Informations produit */}
                    <div className="space-y-6">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900">{product.name}</h1>
                            <div className="mt-2">
                                {product.categories.map((category) => (
                                    <Link
                                        key={category.id}
                                        href={route('categories.show', category.slug)}
                                        className="inline-block mr-2 text-sm text-green-600 hover:text-green-700"
                                    >
                                        {category.name}
                                    </Link>
                                ))}
                            </div>
                            <p className="mt-4 text-xl text-green-600">{product.price} €</p>
                        </div>

                        <div className="space-y-2">
                            {product.dimensions && (
                                <div className="flex items-center space-x-2">
                                    <span className="text-sm text-gray-500">Dimensions:</span>
                                    <span className="text-sm font-medium">{product.dimensions}</span>
                                </div>
                            )}
                            {product.condition && (
                                <div className="flex items-center space-x-2">
                                    <span className="text-sm text-gray-500">État:</span>
                                    <span className="text-sm font-medium">{product.condition}</span>
                                </div>
                            )}
                        </div>

                        <div className="flex space-x-4">
                            <button
                                type="button"
                                className="flex-1 bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                            >
                                <span className="flex items-center justify-center space-x-2">
                                    <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Ajouter au panier</span>
                                </span>
                            </button>
                            <button
                                type="button"
                                className="p-3 rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                            >
                                <svg className="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>

                        <div className="prose prose-sm mt-4">
                            <h3 className="text-lg font-medium">Description</h3>
                            <p>{product.description}</p>
                        </div>
                    </div>
                </div>

                {/* Produits similaires */}
                <div className="mt-16">
                    <h2 className="text-xl font-bold text-gray-900 mb-6">
                        Également dans cette ressourcerie
                    </h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        {similarProducts.map((product) => (
                            <Link
                                key={product.id}
                                href={route('products.show', product.id)}
                                className="group"
                            >
                                <div className="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg">
                                    <img
                                        src={product.image}
                                        alt={product.name}
                                        className="h-full w-full object-cover object-center group-hover:opacity-75"
                                    />
                                </div>
                                <div className="mt-4 space-y-1">
                                    <h3 className="text-sm font-medium text-gray-900">
                                        {product.name}
                                    </h3>
                                    <p className="text-sm text-green-600">{product.price} €</p>
                                </div>
                            </Link>
                        ))}
                    </div>
                </div>
            </div>
        </MainLayout>
    );
} 