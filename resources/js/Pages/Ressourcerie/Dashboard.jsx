import MainLayout from '@/Layouts/MainLayout';
import { Head, Link } from '@inertiajs/react';

export default function Dashboard({ auth, stats, ressourcerie }) {
    return (
        <MainLayout title="Tableau de bord Ressourcerie">
            <Head title="Tableau de bord Ressourcerie" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h1 className="text-2xl font-bold mb-6">Bienvenue, {ressourcerie.name}</h1>
                            
                            {/* Statistiques */}
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                <div className="bg-emerald-50 p-6 rounded-lg">
                                    <h4 className="text-sm font-medium text-emerald-600 mb-2">Total des produits</h4>
                                    <div className="flex items-center">
                                        <p className="text-3xl font-bold text-emerald-700">{stats.total_products}</p>
                                        <Link
                                            href={route('ressourcerie.products.index')}
                                            className="ml-4 text-sm text-emerald-600 hover:text-emerald-800"
                                        >
                                            Voir tous →
                                        </Link>
                                    </div>
                                </div>
                                <div className="bg-blue-50 p-6 rounded-lg">
                                    <h4 className="text-sm font-medium text-blue-600 mb-2">Produits actifs</h4>
                                    <p className="text-3xl font-bold text-blue-700">{stats.active_products}</p>
                                </div>
                                <div className="bg-purple-50 p-6 rounded-lg">
                                    <h4 className="text-sm font-medium text-purple-600 mb-2">Ventes totales</h4>
                                    <p className="text-3xl font-bold text-purple-700">{stats.total_sales}€</p>
                                </div>
                            </div>

                            {/* Actions rapides */}
                            <div className="mb-8">
                                <h2 className="text-xl font-semibold mb-4">Actions rapides</h2>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <Link
                                        href={route('ressourcerie.products.index')}
                                        className="flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 transition-colors"
                                    >
                                        <div className="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                            <svg className="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                            </svg>
                                        </div>
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-gray-900">Gérer mes produits</p>
                                            <p className="text-sm text-gray-500">Voir, ajouter et modifier vos produits</p>
                                        </div>
                                    </Link>

                                    <Link
                                        href={route('ressourcerie.products.create')}
                                        className="flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 transition-colors"
                                    >
                                        <div className="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </div>
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-gray-900">Ajouter un produit</p>
                                            <p className="text-sm text-gray-500">Créer une nouvelle annonce</p>
                                        </div>
                                    </Link>

                                    <Link
                                        href={route('ressourcerie.profile.edit')}
                                        className="flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 transition-colors"
                                    >
                                        <div className="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                            <svg className="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-gray-900">Mon profil</p>
                                            <p className="text-sm text-gray-500">Modifier mes informations</p>
                                        </div>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
} 