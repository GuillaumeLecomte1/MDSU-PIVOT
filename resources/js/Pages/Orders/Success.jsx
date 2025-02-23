import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function Success() {
    return (
        <MainLayout title="Commande réussie">
            <Head title="Commande réussie" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <div className="text-center">
                                <svg
                                    className="mx-auto h-12 w-12 text-green-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>
                                <h2 className="mt-4 text-2xl font-bold">Paiement réussi !</h2>
                                <p className="mt-2 text-gray-600">
                                    Merci pour votre commande. Vous recevrez bientôt un email de confirmation.
                                </p>
                                <div className="mt-6">
                                    <Link
                                        href={route('home')}
                                        className="text-green-600 hover:text-green-700 font-medium"
                                    >
                                        Retour à l'accueil
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