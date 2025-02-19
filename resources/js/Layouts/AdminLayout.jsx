import { Head, Link } from '@inertiajs/react';
import Navigation from '@/Components/Layout/Navigation';

export default function AdminLayout({ children }) {
    return (
        <div className="min-h-screen bg-gray-100">
            <Navigation />

            <header className="bg-white shadow">
                <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center">
                        <h1 className="text-3xl font-bold text-gray-900">
                            Administration
                        </h1>
                        <Link
                            href={route('admin.products.create')}
                            className="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                            </svg>
                            Ajouter un Produit
                        </Link>
                    </div>
                </div>
            </header>

            <main>
                {children}
            </main>
        </div>
    );
} 