import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import ProductCard from '@/Components/Products/ProductCard';
import SearchBar from '@/Components/SearchBar';
import Pagination from '@/Components/Pagination';

export default function Index({ products, categories }) {
    return (
        <AppLayout title="Produits">
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="mb-6">
                        <SearchBar />
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h1 className="text-2xl font-bold">Tous les produits</h1>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                {products.data.map((product) => (
                                    <ProductCard key={product.id} product={product} />
                                ))}
                            </div>

                            <div className="mt-6">
                                <Pagination links={products.links} />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
} 