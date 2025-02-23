import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import CategoryFilter from '@/Components/Categories/CategoryFilter';
import ProductCard from '@/Components/Products/ProductCard';
import SearchBar from '@/Components/SearchBar';
import Pagination from '@/Components/Pagination';

export default function Index({ categories, products, ressourceries, filters: initialFilters }) {
    const defaultFilters = {
        categories: [],
        min_price: '',
        max_price: '',
        city: '',
        search: '',
        color: '',
        quantity: 'all',
        sort: 'recent'
    };

    const [filters, setFilters] = useState({ ...defaultFilters, ...initialFilters });

    const handleFilterChange = (newFilters) => {
        const updatedFilters = { ...filters, ...newFilters };
        setFilters(updatedFilters);
        router.get(route('categories.index'), updatedFilters, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const sortOptions = [
        { value: 'recent', label: 'Plus récent' },
        { value: 'price_asc', label: 'Prix croissant' },
        { value: 'price_desc', label: 'Prix décroissant' }
    ];

    return (
        <MainLayout>
            <Head title="Catégories" />
            
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* En-tête avec la barre de recherche */}
                <div className="flex justify-between items-center mb-8">
                    <div className="flex-1 max-w-xl">
                        <SearchBar 
                            value={filters.search || ''}
                            onChange={(value) => handleFilterChange({ search: value })}
                            placeholder="Rechercher un produit..."
                            className="w-full"
                        />
                    </div>
                    <div className="ml-4">
                        <select
                            value={filters.sort || 'recent'}
                            onChange={(e) => handleFilterChange({ sort: e.target.value })}
                            className="border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                        >
                            {sortOptions.map(option => (
                                <option key={option.value} value={option.value}>
                                    {option.label}
                                </option>
                            ))}
                        </select>
                    </div>
                </div>

                <div className="flex flex-col lg:flex-row gap-8">
                    {/* Filtres */}
                    <CategoryFilter
                        categories={categories}
                        ressourceries={ressourceries}
                        filters={filters}
                        onFilterChange={handleFilterChange}
                    />

                    {/* Grille de produits */}
                    <div className="flex-1">
                        {products.data.length === 0 ? (
                            <div className="text-center py-8">
                                <p className="text-gray-500">Aucun produit trouvé</p>
                            </div>
                        ) : (
                            <>
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                                    {products.data.map((product) => (
                                        <ProductCard key={product.id} product={product} />
                                    ))}
                                </div>
                                <div className="mt-8">
                                    <Pagination links={products.links} />
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </MainLayout>
    );
} 