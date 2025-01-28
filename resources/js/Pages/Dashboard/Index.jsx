import { Head } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import ProductCard from '@/Components/Products/ProductCard';

export default function Index({ latestProducts = [], popularProducts = [], categories = [] }) {
    console.log('Dashboard component loaded', { latestProducts, popularProducts, categories });
    return (
        <AppLayout>
            <Head title="Accueil" />
            
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Section des catégories */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h2 className="text-2xl font-bold">Catégories</h2>
                                <Link 
                                    href={route('categories.index')}
                                    className="text-gray-600 hover:text-gray-900"
                                >
                                    Voir toutes les catégories
                                </Link>
                            </div>
                            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                {categories && categories.map((category) => (
                                    <Link
                                        key={category.id}
                                        href={route('categories.show', category.slug)}
                                        className="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                    >
                                        <h3 className="font-semibold text-lg mb-2">{category.name}</h3>
                                        <p className="text-sm text-gray-600">{category.products_count} produits</p>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Section des derniers produits */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h2 className="text-2xl font-bold">Derniers produits ajoutés</h2>
                                <Link 
                                    href={route('products.index')}
                                    className="text-gray-600 hover:text-gray-900"
                                >
                                    Voir tous les produits
                                </Link>
                            </div>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                {latestProducts && latestProducts.map((product) => (
                                    <ProductCard key={product.id} product={product} />
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Section des produits populaires */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h2 className="text-2xl font-bold">Produits populaires</h2>
                                <Link 
                                    href={route('products.index', { sort: 'popular' })}
                                    className="text-gray-600 hover:text-gray-900"
                                >
                                    Voir plus
                                </Link>
                            </div>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                {popularProducts && popularProducts.map((product) => (
                                    <ProductCard key={product.id} product={product} />
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
} 