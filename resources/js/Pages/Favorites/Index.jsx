import { Head } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import ProductCard from '@/Components/Products/ProductCard';

export default function Index({ favorites }) {
    return (
        <MainLayout>
            <Head title="Mes Favoris" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <h1 className="text-3xl font-bold mb-8">Mes Favoris</h1>

                    {!favorites?.length ? (
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <p className="text-gray-500 text-center">Vous n'avez pas encore de favoris</p>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            {favorites.map((product) => (
                                <ProductCard 
                                    key={product.id} 
                                    product={product} 
                                    inFavoritesPage={true}
                                />
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </MainLayout>
    );
} 