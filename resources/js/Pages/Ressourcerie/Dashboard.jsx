import MainLayout from '@/Layouts/MainLayout';
import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function Dashboard({ auth, stats, ressourcerie, recentOrders, salesData }) {
    const [chart, setChart] = useState(null);

    useEffect(() => {
        const loadChart = async () => {
            const { Line } = await import('react-chartjs-2');
            const { 
                Chart: ChartJS,
                CategoryScale,
                LinearScale,
                PointElement,
                LineElement,
                Title,
                Tooltip,
                Legend,
            } = await import('chart.js');

            ChartJS.register(
                CategoryScale,
                LinearScale,
                PointElement,
                LineElement,
                Title,
                Tooltip,
                Legend
            );

            const chartOptions = {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Évolution des ventes',
                    },
                },
            };

            setChart(<Line options={chartOptions} data={salesData} />);
        };

        loadChart();
    }, [salesData]);

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

    const formatPrice = (price) => {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(price);
    };

    return (
        <MainLayout title="Tableau de bord Ressourcerie">
            <Head title="Tableau de bord Ressourcerie" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h1 className="text-2xl font-bold mb-6">Bienvenue, {ressourcerie.name}</h1>
                            
                            {/* Statistiques */}
                            <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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
                                <div className="bg-orange-50 p-6 rounded-lg">
                                    <h4 className="text-sm font-medium text-orange-600 mb-2">Commandes en attente</h4>
                                    <div className="flex items-center">
                                        <p className="text-3xl font-bold text-orange-700">{stats.pending_orders}</p>
                                        <Link
                                            href={route('ressourcerie.orders.index')}
                                            className="ml-4 text-sm text-orange-600 hover:text-orange-800"
                                        >
                                            Voir toutes →
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            {/* Graphique des ventes */}
                            <div className="mb-8">
                                <div className="bg-white p-6 rounded-lg shadow">
                                    {chart}
                                </div>
                            </div>

                            {/* Actions rapides */}
                            <div className="mb-8">
                                <h2 className="text-xl font-semibold mb-4">Actions rapides</h2>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <Link
                                        href={route('ressourcerie.products.create')}
                                        className="flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 transition-colors"
                                    >
                                        <div className="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                            <svg className="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </div>
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-gray-900">Ajouter un produit</p>
                                            <p className="text-sm text-gray-500">Créer une nouvelle annonce</p>
                                        </div>
                                    </Link>

                                    <Link
                                        href={route('ressourcerie.products.index')}
                                        className="flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 transition-colors"
                                    >
                                        <div className="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                            </svg>
                                        </div>
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-gray-900">Gérer mes produits</p>
                                            <p className="text-sm text-gray-500">Voir et modifier vos produits</p>
                                        </div>
                                    </Link>

                                    <Link
                                        href={route('ressourcerie.orders.index')}
                                        className="flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 transition-colors"
                                    >
                                        <div className="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                            <svg className="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-gray-900">Gérer les commandes</p>
                                            <p className="text-sm text-gray-500">Voir et traiter les commandes</p>
                                        </div>
                                    </Link>
                                </div>
                            </div>

                            {/* Dernières commandes */}
                            <div className="mb-8">
                                <div className="flex items-center justify-between mb-4">
                                    <h2 className="text-xl font-semibold">Dernières commandes</h2>
                                    <Link
                                        href={route('ressourcerie.orders.index')}
                                        className="text-sm text-emerald-600 hover:text-emerald-800"
                                    >
                                        Voir toutes les commandes →
                                    </Link>
                                </div>
                                <div className="bg-white overflow-hidden border border-gray-200 sm:rounded-lg">
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
                                                    Montant
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Statut
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Date
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-gray-200">
                                            {recentOrders.map((order) => (
                                                <tr key={order.id}>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        <Link
                                                            href={route('ressourcerie.orders.show', order.id)}
                                                            className="text-emerald-600 hover:text-emerald-900"
                                                        >
                                                            {order.reference}
                                                        </Link>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {order.client_name}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {formatPrice(order.total)}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <span className={`px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusColor(order.status)}`}>
                                                            {order.status_label}
                                                        </span>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {order.created_at}
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
            </div>
        </MainLayout>
    );
} 