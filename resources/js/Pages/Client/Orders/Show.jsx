import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function OrderShow({ order }) {
    const formatPrice = (price) => {
        return parseFloat(price).toFixed(2);
    };

    const getStatusBadge = (status) => {
        const statusConfig = {
            'pending': {
                text: 'En attente',
                className: 'bg-yellow-100 text-yellow-800',
            },
            'processing': {
                text: 'En cours',
                className: 'bg-blue-100 text-blue-800',
            },
            'completed': {
                text: 'Terminée',
                className: 'bg-green-100 text-green-800',
            },
            'cancelled': {
                text: 'Annulée',
                className: 'bg-red-100 text-red-800',
            },
        };

        const config = statusConfig[status] || statusConfig['pending'];

        return (
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.className}`}>
                {config.text}
            </span>
        );
    };

    return (
        <MainLayout>
            <Head title={`Commande #${order.id}`} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="mb-6">
                        <Link
                            href={route('client.orders.index')}
                            className="text-green-600 hover:text-green-700 flex items-center"
                        >
                            <svg className="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Retour aux commandes
                        </Link>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <div className="border-b pb-4 mb-4">
                                <div className="flex justify-between items-start">
                                    <div>
                                        <h1 className="text-2xl font-semibold">Commande #{order.id}</h1>
                                        <p className="text-gray-600 mt-1">{order.date}</p>
                                    </div>
                                    <div className="text-right">
                                        <div className="mb-2">{getStatusBadge(order.status)}</div>
                                        <p className="text-xl font-bold text-green-600">{formatPrice(order.total)} €</p>
                                    </div>
                                </div>
                            </div>

                            <div className="space-y-8">
                                {/* Liste des produits */}
                                <div>
                                    <h2 className="text-lg font-medium mb-4">Produits commandés</h2>
                                    <div className="space-y-4">
                                        {order.items.map((item) => (
                                            <div key={item.id} className="flex items-center space-x-4 border-b pb-4">
                                                <div className="flex-shrink-0 w-24 h-24">
                                                    <img
                                                        src={item.image}
                                                        alt={item.name}
                                                        className="w-full h-full object-cover rounded-md"
                                                    />
                                                </div>
                                                <div className="flex-1">
                                                    <h3 className="text-lg font-medium">{item.name}</h3>
                                                    <p className="text-sm text-gray-500">
                                                        Ressourcerie: {item.ressourcerie}
                                                    </p>
                                                </div>
                                                <div className="text-right">
                                                    <p className="text-sm text-gray-600">
                                                        Quantité: {item.quantity}
                                                    </p>
                                                    <p className="text-lg font-medium text-gray-900">
                                                        {formatPrice(item.price * item.quantity)} €
                                                    </p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>

                                {/* Informations de paiement */}
                                <div>
                                    <h2 className="text-lg font-medium mb-4">Informations de paiement</h2>
                                    <div className="bg-gray-50 rounded-lg p-4">
                                        <p className="text-sm text-gray-600">
                                            ID de transaction: {order.payment_intent_id}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
} 