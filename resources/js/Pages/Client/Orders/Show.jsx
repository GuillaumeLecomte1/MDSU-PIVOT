import { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { toast } from 'react-toastify';

export default function Show({ order: initialOrder }) {
    const [order, setOrder] = useState(initialOrder);
    const { patch, processing } = useForm();

    useEffect(() => {
        // S'assurer que Echo est disponible
        if (window.Echo) {
            // Écouter les mises à jour de statut en temps réel
            const channel = window.Echo.private(`orders.${order.user_id}`)
                .listen('.order.status.updated', (e) => {
                    if (e.order_id === order.id) {
                        setOrder(prevOrder => ({
                            ...prevOrder,
                            status: e.new_status,
                            // Mettre à jour can_complete en fonction du nouveau statut
                            can_complete: e.new_status === 'delivered'
                        }));
                        
                        toast.info(`Le statut de votre commande a été mis à jour : ${e.status_label}`);
                    }
                });

            return () => {
                channel.stopListening('.order.status.updated');
            };
        }
    }, [order.id, order.user_id]);

    // Forcer la mise à jour de can_complete si le statut est 'delivered'
    useEffect(() => {
        if (order.status === 'delivered' && !order.can_complete) {
            setOrder(prevOrder => ({
                ...prevOrder,
                can_complete: true
            }));
        }
    }, [order.status]);

    const formatPrice = (price) => {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(price);
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

    const handleConfirmReception = () => {
        if (confirm('Êtes-vous sûr de vouloir confirmer la réception de cette commande ?')) {
            patch(route('client.orders.confirm-reception', order.id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Réception de la commande confirmée avec succès');
                },
                onError: () => {
                    toast.error('Une erreur est survenue lors de la confirmation');
                }
            });
        }
    };

    return (
        <MainLayout>
            <Head title={`Commande #${order.id}`} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="mb-6">
                        <Link
                            href={route('client.orders.index')}
                            className="text-emerald-600 hover:text-emerald-700 flex items-center"
                        >
                            <svg className="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Retour aux commandes
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
                                <div className="text-right">
                                    <div className="mb-4">
                                        <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(order.status)}`}>
                                            {getStatusLabel(order.status)}
                                        </span>
                                    </div>
                                    {order.can_complete && (
                                        <button
                                            onClick={handleConfirmReception}
                                            disabled={processing}
                                            className="bg-emerald-600 text-white px-4 py-2 rounded-md hover:bg-emerald-700 disabled:opacity-50"
                                        >
                                            {processing ? 'Confirmation...' : 'Confirmer la réception'}
                                        </button>
                                    )}
                                </div>
                            </div>

                            {/* Produits */}
                            <div className="mt-8">
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
                                            {order.products && order.products.map((product) => (
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
                                                                <div className="text-sm text-gray-500">
                                                                    {product.ressourcerie.name}
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