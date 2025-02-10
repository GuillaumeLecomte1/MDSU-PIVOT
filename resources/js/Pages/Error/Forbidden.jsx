import { Link } from '@inertiajs/react';

export default function Forbidden() {
    return (
        <div className="min-h-screen bg-gray-100 flex flex-col justify-center items-center">
            <div className="text-center">
                <h1 className="text-6xl font-bold text-red-600 mb-4">403</h1>
                <h2 className="text-3xl font-semibold text-gray-800 mb-4">Accès Interdit</h2>
                <p className="text-gray-600 mb-8">
                    Désolé, vous n'avez pas les permissions nécessaires pour accéder à cette page.
                </p>
                <Link
                    href={route('home')}
                    className="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Retour à l'accueil
                </Link>
            </div>
        </div>
    );
} 