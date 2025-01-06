import { Head, usePage } from '@inertiajs/react';
import Navigation from '@/Components/Navigation';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

export default function AppLayout({ title, children }) {
    const { flash } = usePage().props;

    return (
        <div className="min-h-screen bg-gray-100">
            <Head title={title} />
            <Navigation />
            
            <main className="min-h-screen">
                {children}
            </main>

            <ToastContainer position="bottom-right" />
        </div>
    );
} 