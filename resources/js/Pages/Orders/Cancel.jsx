import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function Cancel() {
    return (
        <MainLayout title="Paiement annulé">
            <Head title="Paiement annulé" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <div className="text-center">
                                <svg
                                    className="mx-auto h-12 w-12 text-red-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                                <h2 className="mt-4 text-2xl font-bold">Paiement annulé</h2>
                                <p className="mt-2 text-gray-600">
                                    Le paiement a été annulé. Votre panier a été conservé.
                                </p>
                                <div className="mt-6 space-x-4">
                                    <Link
                                        href={route('cart.index')}
                                        className="text-green-600 hover:text-green-700 font-medium"
                                    >
                                        Retour au panier
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