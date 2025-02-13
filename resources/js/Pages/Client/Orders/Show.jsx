import AppLayout from '@/Layouts/AppLayout';
import { Head, Link } from '@inertiajs/react';

export default function OrderShow() {
    // Données statiques pour l'exemple
    const fakeOrder = {
        id: 'CMD-2024-001',
        date: '15 Mars 2024',
        status: 'completed',
        total: 156.90,
        payment: {
            status: 'paid',
            method: 'Stripe',
            cardLast4: '4242',
            transactionId: 'pi_3O9X4Z2eZvKYlo2C1J8F9K2M'
        },
        shipping: {
            name: 'John Doe',
            address: '123 Rue de Paris',
            city: 'Paris',
            postalCode: '75001',
            country: 'France'
        },
        items: [
            {
                id: 1,
                name: 'Table basse vintage',
                price: 89.90,
                quantity: 1,
                image: '/storage/products/table.jpg',
                ressourcerie: 'Ressourcerie du Centre'
            },
            {
                id: 2,
                name: 'Lampe de bureau',
                price: 67.00,
                quantity: 1,
                image: '/storage/products/lampe.jpg',
                ressourcerie: 'Ressourcerie du Centre'
            }
        ]
    };

    const getStatusBadgeColor = (status) => {
        const colors = {
            completed: 'bg-green-100 text-green-800',
            pending: 'bg-yellow-100 text-yellow-800',
            cancelled: 'bg-red-100 text-red-800'
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    };

    return (
        <AppLayout title={`Commande ${fakeOrder.id}`}>
            <Head title={`Commande ${fakeOrder.id}`} />

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
                                    Commande {fakeOrder.id}
                                </h1>
                                <span className={`px-3 py-1 rounded-full text-sm font-semibold ${getStatusBadgeColor(fakeOrder.status)}`}>
                                    {fakeOrder.status === 'completed' ? 'Payée' : 'En attente'}
                                </span>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                {/* Informations de commande */}
                                <div className="bg-gray-50 p-4 rounded-lg">
                                    <h2 className="text-lg font-medium mb-4">Informations de commande</h2>
                                    <div className="space-y-2">
                                        <p><span className="font-medium">Date :</span> {fakeOrder.date}</p>
                                        <p><span className="font-medium">Numéro de commande :</span> {fakeOrder.id}</p>
                                        <p><span className="font-medium">Total :</span> {fakeOrder.total.toFixed(2)}€</p>
                                    </div>
                                </div>

                                {/* Informations de paiement */}
                                <div className="bg-gray-50 p-4 rounded-lg">
                                    <h2 className="text-lg font-medium mb-4">Paiement</h2>
                                    <div className="space-y-2">
                                        <p><span className="font-medium">Méthode :</span> {fakeOrder.payment.method}</p>
                                        <p><span className="font-medium">Carte :</span> •••• {fakeOrder.payment.cardLast4}</p>
                                        <p><span className="font-medium">Transaction :</span> {fakeOrder.payment.transactionId}</p>
                                    </div>
                                </div>
                            </div>

                            {/* Adresse de livraison */}
                            <div className="mb-8">
                                <h2 className="text-lg font-medium mb-4">Adresse de livraison</h2>
                                <div className="bg-gray-50 p-4 rounded-lg">
                                    <p>{fakeOrder.shipping.name}</p>
                                    <p>{fakeOrder.shipping.address}</p>
                                    <p>{fakeOrder.shipping.postalCode} {fakeOrder.shipping.city}</p>
                                    <p>{fakeOrder.shipping.country}</p>
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
                                            {fakeOrder.items.map((item) => (
                                                <tr key={item.id}>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="flex items-center">
                                                            <div className="h-10 w-10 flex-shrink-0">
                                                                <img
                                                                    className="h-10 w-10 rounded-full object-cover"
                                                                    src="https://via.placeholder.com/150"
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
                                                    {fakeOrder.total.toFixed(2)}€
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