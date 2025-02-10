import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
export default function Dashboard({ auth, stats }) {
    return (
        <AppLayout title="Administration">
            <Head title="Administration" />


            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h3 className="text-lg font-medium mb-4">Statistiques</h3>
                            
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div className="bg-gray-100 p-4 rounded-lg">
                                    <h4 className="text-sm font-medium text-gray-500">Utilisateurs</h4>
                                    <p className="text-2xl font-bold">{stats.users}</p>
                                </div>
                                <div className="bg-gray-100 p-4 rounded-lg">
                                    <h4 className="text-sm font-medium text-gray-500">Produits</h4>
                                    <p className="text-2xl font-bold">{stats.products}</p>
                                </div>
                                <div className="bg-gray-100 p-4 rounded-lg">
                                    <h4 className="text-sm font-medium text-gray-500">Commandes</h4>
                                    <p className="text-2xl font-bold">{stats.orders}</p>
                                </div>
                            </div>

                            <div className="mt-8">
                                <h3 className="text-lg font-medium mb-4">Actions rapides</h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <a
                                        href={route('admin.users.index')}
                                        className="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    >
                                        Gérer les utilisateurs
                                    </a>
                                    <a
                                        href={route('admin.products.index')}
                                        className="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                    >
                                        Gérer les produits
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
} 