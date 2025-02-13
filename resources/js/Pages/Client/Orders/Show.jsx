import AppLayout from '@/Layouts/AppLayout';
import { Head, Link } from '@inertiajs/react';

export default function OrderShow({ order }) {
    const getStatusBadgeColor = (status) => {
        const colors = {
            completed: 'bg-green-100 text-green-800',
            pending: 'bg-yellow-100 text-yellow-800',
            cancelled: 'bg-red-100 text-red-800'
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    };

    const getStatusLabel = (status) => {
        const labels = {
            completed: 'Payée',
            pending: 'En attente',
            cancelled: 'Annulée'
        };
        return labels[status] || status;
    };

    return (
        <AppLayout title={`Commande ${order.id}`}>
            <Head title={`Commande ${order.id}`} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="mb-6">
                        <Link
                            href={route('client.orders.index')}
                            className="text-indigo-600 hover:text-indigo-900"
                        >
                            ← Retour aux commandes
                        </Link>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h1 className="text-2xl font-semibold">
                                    Commande {order.id}
                                </h1>
                                <span className={`px-3 py-1 rounded-full text-sm font-semibold ${getStatusBadgeColor(order.status)}`}>
                                    {getStatusLabel(order.status)}
                                </span>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                {/* Informations de commande */}
                                <div className="bg-gray-50 p-4 rounded-lg">
                                    <h2 className="text-lg font-medium mb-4">Informations de commande</h2>
                                    <div className="space-y-2">
                                        <p><span className="font-medium">Date :</span> {order.date}</p>
                                        <p><span className="font-medium">Numéro de commande :</span> {order.id}</p>
                                        <p><span className="font-medium">Total :</span> {order.total.toFixed(2)}€</p>
                                    </div>
                                </div>

                                {/* Informations de paiement */}
                                <div className="bg-gray-50 p-4 rounded-lg">
                                    <h2 className="text-lg font-medium mb-4">Paiement</h2>
                                    <div className="space-y-2">
                                        <p><span className="font-medium">Méthode :</span> {order.payment.method}</p>
                                        <p><span className="font-medium">Carte :</span> •••• {order.payment.cardLast4}</p>
                                        <p><span className="font-medium">Transaction :</span> {order.payment.transactionId}</p>
                                    </div>
                                </div>
                            </div>

                            {/* Adresse de livraison */}
                            <div className="mb-8">
                                <h2 className="text-lg font-medium mb-4">Adresse de livraison</h2>
                                <div className="bg-gray-50 p-4 rounded-lg">
                                    <p>{order.shipping.name}</p>
                                    <p>{order.shipping.address}</p>
                                    <p>{order.shipping.postalCode} {order.shipping.city}</p>
                                    <p>{order.shipping.country}</p>
                                </div>
                            </div>

                            {/* Produits */}
                            <div>
                                <h2 className="text-lg font-medium mb-4">Produits commandés</h2>
                                <div className="border rounded-lg overflow-hidden">
                                    <table className="min-w-full divide-y divide-gray-200">
                                        <thead className="bg-gray-50">
                                            <tr>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Produit
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Ressourcerie
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Quantité
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Prix
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-gray-200">
                                            {order.items.map((item) => (
                                                <tr key={item.id}>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="flex items-center">
                                                            <div className="h-10 w-10 flex-shrink-0">
                                                                <img
                                                                    className="h-10 w-10 rounded-full object-cover"
                                                                    src={item.image}
                                                                    alt={item.name}
                                                                />
                                                            </div>
                                                            <div className="ml-4">
                                                                <div className="text-sm font-medium text-gray-900">
                                                                    {item.name}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="text-sm text-gray-900">{item.ressourcerie}</div>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="text-sm text-gray-900">{item.quantity}</div>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="text-sm text-gray-900">{item.price.toFixed(2)}€</div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                        <tfoot className="bg-gray-50">
                                            <tr>
                                                <td colSpan="3" className="px-6 py-4 text-right font-medium">
                                                    Total
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap font-medium">
                                                    {order.total.toFixed(2)}€
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
        </AppLayout>
    );
} 