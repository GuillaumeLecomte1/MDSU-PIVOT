import MainLayout from '@/Layouts/MainLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ auth, stats, ressourcerie }) {
    return (
        <MainLayout title="Tableau de bord Ressourcerie">
            <Head title="Tableau de bord Ressourcerie" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <h3 className="text-lg font-medium mb-4">Bienvenue, {ressourcerie.name}</h3>
                            
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div className="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                    <h4 className="text-sm font-medium text-gray-500 dark:text-gray-400">Total des produits</h4>
                                    <p className="text-2xl font-bold">{stats.total_products}</p>
                                </div>
                                <div className="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                    <h4 className="text-sm font-medium text-gray-500 dark:text-gray-400">Produits actifs</h4>
                                    <p className="text-2xl font-bold">{stats.active_products}</p>
                                </div>
                                <div className="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                    <h4 className="text-sm font-medium text-gray-500 dark:text-gray-400">Ventes totales</h4>
                                    <p className="text-2xl font-bold">{stats.total_sales}€</p>
                                </div>
                            </div>

                            <div className="mt-8">
                                <h3 className="text-lg font-medium mb-4">Actions rapides</h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <a
                                        href={route('ressourcerie.products.create')}
                                        className="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                    >
                                        Ajouter un produit
                                    </a>
                                    <a
                                        href={route('ressourcerie.profile.edit')}
                                        className="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                                    >
                                        Modifier mon profil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
} 