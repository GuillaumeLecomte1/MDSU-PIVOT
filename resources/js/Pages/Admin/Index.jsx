import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Index() {
    return (
        <AdminLayout>
            <Head title="Administration" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Section Accès Rapide */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                        <div className="p-6">
                            <h2 className="text-2xl font-bold mb-6">Accès Rapide</h2>
                            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                <Link
                                    href={route('admin.products.create')}
                                    className="flex flex-col items-center p-6 bg-green-50 rounded-lg hover:bg-green-100 transition-colors border-2 border-green-500"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-12 w-12 text-green-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span className="font-bold text-green-700">Ajouter un Produit</span>
                                </Link>

                                <Link
                                    href={route('admin.products.index')}
                                    className="flex flex-col items-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-12 w-12 text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <span className="font-bold">Gérer les Produits</span>
                                </Link>

                                <Link
                                    href={route('admin.categories.index')}
                                    className="flex flex-col items-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-12 w-12 text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    <span className="font-bold">Gérer les Catégories</span>
                                </Link>

                                <Link
                                    href={route('admin.ressourceries.index')}
                                    className="flex flex-col items-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-12 w-12 text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span className="font-bold">Gérer les Ressourceries</span>
                                </Link>

                                <Link
                                    href={route('admin.users.index')}
                                    className="flex flex-col items-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-12 w-12 text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span className="font-bold">Gérer les Utilisateurs</span>
                                </Link>

                                <Link
                                    href={route('admin.orders.index')}
                                    className="flex flex-col items-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-12 w-12 text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span className="font-bold">Gérer les Commandes</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Section Statistiques */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h2 className="text-2xl font-bold mb-6">Statistiques</h2>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div className="bg-blue-50 rounded-lg p-6">
                                    <div className="text-blue-500 text-4xl font-bold mb-2">0</div>
                                    <div className="text-gray-600">Produits en ligne</div>
                                </div>
                                <div className="bg-green-50 rounded-lg p-6">
                                    <div className="text-green-500 text-4xl font-bold mb-2">0</div>
                                    <div className="text-gray-600">Commandes du jour</div>
                                </div>
                                <div className="bg-yellow-50 rounded-lg p-6">
                                    <div className="text-yellow-500 text-4xl font-bold mb-2">0</div>
                                    <div className="text-gray-600">Utilisateurs actifs</div>
                                </div>
                                <div className="bg-purple-50 rounded-lg p-6">
                                    <div className="text-purple-500 text-4xl font-bold mb-2">0</div>
                                    <div className="text-gray-600">Ressourceries</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
} 