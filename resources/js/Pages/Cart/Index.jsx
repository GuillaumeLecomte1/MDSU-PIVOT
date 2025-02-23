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
        return products.reduce((total, product) => total + (product.price * product.quantity), 0);
    };

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
                                        {products.map((product) => (
                                            <div key={product.id} className="flex items-center space-x-4 border-b pb-4">
                                                <div className="flex-shrink-0 w-24 h-24">
                                                    <img
                                                        src={product.images?.[0]?.url || '/images/placeholder.jpg'}
                                                        alt={product.name}
                                                        className="w-full h-full object-cover rounded-md"
                                                    />
                                                </div>
                                                
                                                <div className="flex-1">
                                                    <h3 className="text-lg font-medium text-gray-900">
                                                        {product.name}
                                                    </h3>
                                                    <p className="text-sm text-gray-500">
                                                        Prix unitaire: {product.price} €
                                                    </p>
                                                </div>

                                                <div className="flex items-center space-x-4">
                                                    <div className="flex items-center border rounded-md">
                                                        <button
                                                            onClick={() => updateQuantity(product.id, Math.max(1, product.quantity - 1))}
                                                            disabled={updatingQuantity || product.quantity <= 1}
                                                            className="px-3 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-50"
                                                        >
                                                            -
                                                        </button>
                                                        <span className="px-3 py-1 text-gray-700">
                                                            {product.quantity}
                                                        </span>
                                                        <button
                                                            onClick={() => updateQuantity(product.id, product.quantity + 1)}
                                                            disabled={updatingQuantity}
                                                            className="px-3 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-50"
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
                                        ))}
                                    </div>

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
                                                className="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                            >
                                                Passer la commande
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