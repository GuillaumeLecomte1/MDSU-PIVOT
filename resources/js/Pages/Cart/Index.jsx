import { Head } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function Cart({ auth }) {
    return (
        <MainLayout title="Mon Panier">
            <Head title="Mon Panier" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h1 className="text-2xl font-semibold mb-6">Mon Panier</h1>
                            <div className="text-gray-500">
                                Votre panier est vide.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
} 