import Navigation from '@/Components/Navigation';
import { Head } from '@inertiajs/react';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

export default function AppLayout({ title, children }) {
    return (
        <div className="min-h-screen bg-gray-100">
            <Head title={title} />
            
            <Navigation />

            <main>{children}</main>

            <ToastContainer position="bottom-right" />
        </div>
    );
} 