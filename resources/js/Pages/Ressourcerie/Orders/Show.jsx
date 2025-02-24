import { useState, useEffect } from 'react';
import { Head, Link, useForm, router, usePage } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { toast } from 'react-toastify';

export default function Show({ order: initialOrder }) {
    const [order, setOrder] = useState(initialOrder);
    const { flash } = usePage().props;

    // Afficher les messages flash
    useEffect(() => {
        if (flash.success) {
            toast.success(flash.success);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
    }, [flash]);

    const formatPrice = (price) => {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(price);
    };

    const handleStatusChange = (e) => {
        const newStatus = e.target.value;
        
        router.patch(route('ressourcerie.orders.updateStatus', order.id), {
            status: newStatus
        }, {
            preserveScroll: true,
            onSuccess: () => {
                setOrder(prevOrder => ({
                    ...prevOrder,
                    status: newStatus
                }));
            },
        });
    };

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

    return (
        <MainLayout title="Détails de la commande">
            <Head title="Détails de la commande" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="mb-6">
                        <Link
                            href={route('ressourcerie.orders.index')}
                            className="text-emerald-600 hover:text-emerald-700"
                        >
                            ← Retour aux commandes
                        </Link>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex justify-between items-start mb-6">
                                <div>
                                    <h1 className="text-2xl font-bold mb-2">
                                        Commande #{order.id}
                                    </h1>
                                    <p className="text-gray-600">
                                        Passée le {order.date}
                                    </p>
                                </div>
                                <div className="flex items-center space-x-4">
                                    {order.status !== 'completed' && order.status !== 'cancelled' ? (
                                        <select
                                            value={order.status}
                                            onChange={handleStatusChange}
                                            className="rounded-md border-gray-300"
                                        >
                                            <option value="pending">En attente</option>
                                            <option value="processing">En traitement</option>
                                            <option value="ready">Prête</option>
                                            <option value="delivered">Livrée</option>
                                            <option value="cancelled">Annulée</option>
                                        </select>
                                    ) : null}
                                    <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(order.status)}`}>
                                        {getStatusLabel(order.status)}
                                    </span>
                                </div>
                            </div>

                            {/* Informations client */}
                            <div className="mb-8">
                                <h2 className="text-lg font-semibold mb-4">Informations client</h2>
                                <div className="bg-gray-50 p-4 rounded-lg">
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <p className="text-sm text-gray-600">Nom</p>
                                            <p className="font-medium">{order.customer.name}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-gray-600">Email</p>
                                            <p className="font-medium">{order.customer.email}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Produits */}
                            <div className="mb-8">
                                <h2 className="text-lg font-semibold mb-4">Produits commandés</h2>
                                <div className="bg-gray-50 rounded-lg overflow-hidden">
                                    <table className="min-w-full divide-y divide-gray-200">
                                        <thead className="bg-gray-100">
                                            <tr>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Produit
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Prix unitaire
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Quantité
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Total
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-gray-200">
                                            {order.products.map((product) => (
                                                <tr key={product.id}>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="flex items-center">
                                                            {product.image && (
                                                                <img
                                                                    src={`/storage/${product.image}`}
                                                                    alt={product.name}
                                                                    className="w-12 h-12 object-cover rounded mr-4"
                                                                />
                                                            )}
                                                            <div>
                                                                <div className="text-sm font-medium text-gray-900">
                                                                    {product.name}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="text-sm text-gray-900">
                                                            {formatPrice(product.price)}
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="text-sm text-gray-900">
                                                            {product.quantity}
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="text-sm text-gray-900">
                                                            {formatPrice(product.price * product.quantity)}
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                        <tfoot className="bg-gray-50">
                                            <tr>
                                                <td colSpan="3" className="px-6 py-4 text-right font-medium">
                                                    Total
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-lg font-bold text-gray-900">
                                                        {formatPrice(order.total)}
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
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