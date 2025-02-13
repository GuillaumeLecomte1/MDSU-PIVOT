import { Head } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function Index({ ressourceries }) {
    return (
        <MainLayout title="Ressourceries">
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h1 className="text-2xl font-bold mb-6">Nos Ressourceries</h1>
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {ressourceries.map((ressourcerie) => (
                                    <Link
                                        key={ressourcerie.id}
                                        href={route('ressourceries.show', ressourcerie.slug)}
                                        className="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200"
                                    >
                                        <div className="p-6">
                                            <h2 className="text-xl font-semibold mb-2">{ressourcerie.name}</h2>
                                            <p className="text-gray-600 mb-4">{ressourcerie.description}</p>
                                            <div className="text-sm text-gray-500">
                                                <p>{ressourcerie.address}</p>
                                                <p>{ressourcerie.city}</p>
                                                <p>{ressourcerie.phone}</p>
                                            </div>
                                            <div className="mt-4 text-sm text-gray-600">
                                                {ressourcerie.products_count} produits disponibles
                                            </div>
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
} 