import Navigation from '@/Components/Navigation';
import { Head } from '@inertiajs/react';

export default function GuestLayout({ title, children }) {
    return (
        <div className="min-h-screen bg-gray-100">
            <Head title={title} />
            
            <Navigation />

            <main>
                <div className="w-full sm:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {children}
                </div>
            </main>
        </div>
    );
}
