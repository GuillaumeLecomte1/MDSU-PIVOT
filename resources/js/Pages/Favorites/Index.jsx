import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import ProductCard from '@/Components/Products/ProductCard';

export default function Index({ favorites }) {
    return (
        <AppLayout title="Mes Favoris">
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h1 className="text-2xl font-bold mb-6">Mes Favoris</h1>

                            {favorites.length === 0 ? (
                                <div className="text-center py-8">
                                    <p className="text-gray-500">Vous n'avez pas encore de favoris</p>
                                </div>
                            ) : (
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                    {favorites.map((favorite) => (
                                        <ProductCard key={favorite.id} product={favorite.product} />
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
} 