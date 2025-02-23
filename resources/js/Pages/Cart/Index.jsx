import { Head, router } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { useState } from 'react';

export default function Cart({ products }) {
    const [updatingQuantity, setUpdatingQuantity] = useState(false);

    const updateQuantity = (productId, newQuantity) => {
        if (updatingQuantity) return;
        
        setUpdatingQuantity(true);
        router.patch(route('cart.update', productId), {
            quantity: newQuantity
        }, {
            preserveScroll: true,
            onFinish: () => setUpdatingQuantity(false)
        });
    };

    const removeFromCart = (productId) => {
        if (updatingQuantity) return;

        setUpdatingQuantity(true);
        router.delete(route('cart.remove', productId), {
            preserveScroll: true,
            onFinish: () => setUpdatingQuantity(false)
        });
    };

    const calculateTotal = () => {
        return products
            .filter(product => product.is_available && product.stock > 0)
            .reduce((total, product) => total + (product.price * product.quantity), 0);
    };

    const hasUnavailableProducts = products.some(product => !product.is_available || product.stock <= 0);
    const availableProducts = products.filter(product => product.is_available && product.stock > 0);

    return (
        <MainLayout title="Mon Panier">
            <Head title="Mon Panier" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h1 className="text-2xl font-semibold mb-6">Mon Panier</h1>
                            
                            {!products || products.length === 0 ? (
                                <div className="text-gray-500">
                                    Votre panier est vide.
                                </div>
                            ) : (
                                <div className="space-y-8">
                                    {/* Liste des produits */}
                                    <div className="space-y-4">
                                        {products.map((product) => {
                                            const isProductAvailable = product.is_available && product.stock > 0;
                                            
                                            return (
                                                <div 
                                                    key={product.id} 
                                                    className={`flex items-center space-x-4 border-b pb-4 ${!isProductAvailable ? 'bg-red-50' : ''}`}
                                                >
                                                    <div className="flex-shrink-0 w-24 h-24">
                                                        <img
                                                            src={product.images?.[0]?.url || '/images/placeholder.jpg'}
                                                            alt={product.name}
                                                            className={`w-full h-full object-cover rounded-md ${!isProductAvailable ? 'opacity-50' : ''}`}
                                                        />
                                                    </div>
                                                    
                                                    <div className="flex-1">
                                                        <h3 className="text-lg font-medium text-gray-900">
                                                            {product.name}
                                                        </h3>
                                                        <p className="text-sm text-gray-500">
                                                            Prix unitaire: {product.price} €
                                                        </p>
                                                        {!isProductAvailable && (
                                                            <p className="text-sm text-red-600 font-medium mt-1">
                                                                {!product.is_available ? 'Produit indisponible' : 'Produit en rupture de stock'}
                                                            </p>
                                                        )}
                                                    </div>

                                                    <div className="flex items-center space-x-4">
                                                        <div className="flex items-center border rounded-md">
                                                            <button
                                                                onClick={() => updateQuantity(product.id, Math.max(1, product.quantity - 1))}
                                                                disabled={updatingQuantity || product.quantity <= 1 || !isProductAvailable}
                                                                className={`px-3 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-50 
                                                                    ${!isProductAvailable ? 'cursor-not-allowed' : ''}`}
                                                            >
                                                                -
                                                            </button>
                                                            <span className="px-3 py-1 text-gray-700">
                                                                {product.quantity}
                                                            </span>
                                                            <button
                                                                onClick={() => updateQuantity(product.id, product.quantity + 1)}
                                                                disabled={updatingQuantity || !isProductAvailable || (product.stock && product.quantity >= product.stock)}
                                                                className={`px-3 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-50 
                                                                    ${!isProductAvailable ? 'cursor-not-allowed' : ''}`}
                                                            >
                                                                +
                                                            </button>
                                                        </div>

                                                        <button
                                                            onClick={() => removeFromCart(product.id)}
                                                            disabled={updatingQuantity}
                                                            className="text-red-600 hover:text-red-800 disabled:opacity-50"
                                                        >
                                                            <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>

                                    {/* Avertissement pour les produits indisponibles */}
                                    {hasUnavailableProducts && (
                                        <div className="bg-red-50 border border-red-200 rounded-md p-4">
                                            <div className="flex">
                                                <div className="flex-shrink-0">
                                                    <svg className="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </div>
                                                <div className="ml-3">
                                                    <h3 className="text-sm font-medium text-red-800">
                                                        Attention
                                                    </h3>
                                                    <div className="mt-2 text-sm text-red-700">
                                                        <p>
                                                            Certains produits de votre panier sont indisponibles ou en rupture de stock.
                                                            Ils ne seront pas inclus dans votre commande.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* Total et actions */}
                                    <div className="border-t pt-4">
                                        <div className="flex justify-between items-center">
                                            <span className="text-lg font-medium">Total:</span>
                                            <span className="text-xl font-bold text-green-600">
                                                {calculateTotal().toFixed(2)} €
                                            </span>
                                        </div>
                                        <div className="mt-6 flex justify-end">
                                            <button
                                                type="button"
                                                disabled={availableProducts.length === 0}
                                                className={`px-6 py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2
                                                    ${availableProducts.length === 0
                                                        ? 'bg-gray-300 cursor-not-allowed'
                                                        : 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500'
                                                    }`}
                                            >
                                                {availableProducts.length === 0 ? 'Aucun produit disponible' : 'Passer la commande'}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
} 