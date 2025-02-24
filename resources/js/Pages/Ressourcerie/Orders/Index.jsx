import { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function Index({ orders, filters }) {
    const [localFilters, setLocalFilters] = useState({
        search: filters?.search || '',
        status: filters?.status || 'all',
        date: filters?.date || 'all'
    });

    // Fonction pour appliquer les filtres
    const applyFilters = () => {
        router.get(route('ressourcerie.orders.index'), localFilters, {
            preserveState: true,
            replace: true,
            only: ['orders', 'filters']
        });
    };

    // Appliquer les filtres après un délai pour éviter trop de requêtes
    useEffect(() => {
        const timeoutId = setTimeout(() => {
            applyFilters();
        }, 500);
        
        return () => clearTimeout(timeoutId);
    }, [localFilters]);

    const getStatusColor = (status) => {
        return {
            pending: 'bg-yellow-100 text-yellow-800',
            processing: 'bg-blue-100 text-blue-800',
            ready: 'bg-indigo-100 text-indigo-800',
            delivered: 'bg-purple-100 text-purple-800',
            completed: 'bg-green-100 text-green-800',
            cancelled: 'bg-red-100 text-red-800'
        }[status] || 'bg-gray-100 text-gray-800';
    };

    const getStatusLabel = (status) => {
        return {
            pending: 'En attente',
            processing: 'En traitement',
            ready: 'Prête',
            delivered: 'Livrée',
            completed: 'Terminée',
            cancelled: 'Annulée'
        }[status] || 'Inconnu';
    };

    const formatPrice = (price) => {
        // S'assurer que price est un nombre
        const numericPrice = typeof price === 'string' ? parseFloat(price.replace(/[^\d.,]/g, '')) : parseFloat(price);
        
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(numericPrice);
    };

    return (
        <MainLayout>
            <Head title="Gestion des commandes" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h1 className="text-2xl font-semibold mb-6">Gestion des commandes</h1>

                            {/* Filtres */}
                            <div className="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <input
                                        type="text"
                                        placeholder="Rechercher une commande..."
                                        className="w-full rounded-md border-gray-300"
                                        value={localFilters.search}
                                        onChange={(e) => setLocalFilters({ ...localFilters, search: e.target.value })}
                                    />
                                </div>
                                <div>
                                    <select
                                        className="w-full rounded-md border-gray-300"
                                        value={localFilters.status}
                                        onChange={(e) => setLocalFilters({ ...localFilters, status: e.target.value })}
                                    >
                                        <option value="all">Tous les statuts</option>
                                        <option value="pending">En attente</option>
                                        <option value="processing">En traitement</option>
                                        <option value="ready">Prête</option>
                                        <option value="delivered">Livrée</option>
                                        <option value="completed">Terminées</option>
                                        <option value="cancelled">Annulées</option>
                                    </select>
                                </div>
                                <div>
                                    <select
                                        className="w-full rounded-md border-gray-300"
                                        value={localFilters.date}
                                        onChange={(e) => setLocalFilters({ ...localFilters, date: e.target.value })}
                                    >
                                        <option value="all">Toutes les dates</option>
                                        <option value="today">Aujourd'hui</option>
                                        <option value="week">Cette semaine</option>
                                        <option value="month">Ce mois</option>
                                    </select>
                                </div>
                            </div>

                            {/* Table des commandes */}
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Commande
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Client
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Statut
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Total
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {orders.map((order) => (
                                            <tr key={order.id}>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {order.reference}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {order.client_name}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {order.created_at}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusColor(order.status)}`}>
                                                        {order.status_label}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {formatPrice(order.total)}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <Link
                                                        href={route('ressourcerie.orders.show', order.id)}
                                                        className="text-emerald-600 hover:text-emerald-900"
                                                    >
                                                        Voir détails
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
} 