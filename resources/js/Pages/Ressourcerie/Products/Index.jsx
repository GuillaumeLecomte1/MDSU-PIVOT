import { Head } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import ProductCard from '@/Components/Products/ProductCard';
import Pagination from '@/Components/Common/Pagination';

export default function Index({ products }) {
    return (
        <MainLayout title="Mes Produits">
            <Head title="Mes Produits" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h1 className="text-2xl font-bold">Mes Produits</h1>
                                <Link
                                    href={route('ressourcerie.products.create')}
                                    className="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    Ajouter un produit
                                </Link>
                            </div>

                            {products.data.length > 0 ? (
                                <>
                                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        {products.data.map((product) => (
                                            <ProductCard
                                                key={product.id}
                                                product={product}
                                                showEditButton={true}
                                                editRoute="ressourcerie.products.edit"
                                            />
                                        ))}
                                    </div>
                                    
                                    {/* Pagination */}
                                    {products.links && products.links.length > 3 && (
                                        <div className="mt-6">
                                            <Pagination links={products.links} />
                                        </div>
                                    )}
                                </>
                            ) : (
                                <div className="text-center py-12">
                                    <p className="text-gray-500 text-lg">Vous n'avez pas encore de produits.</p>
                                    <Link
                                        href={route('ressourcerie.products.create')}
                                        className="inline-flex items-center px-4 py-2 mt-4 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    >
                                        Ajouter votre premier produit
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
} 