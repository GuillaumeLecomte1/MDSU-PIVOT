import Navigation from '@/Components/Navigation';
import { Head } from '@inertiajs/react';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import ErrorBoundary from '@/Components/Common/Feedback/ErrorBoundary';
import PageTransition from '@/Components/Common/Transitions/PageTransition';
import PageLoader from '@/Components/Common/Loading/PageLoader';
import { useLoading } from '@/Hooks/useLoading';
import { useEffect } from 'react';

export default function MainLayout({ title, children }) {
    const { isLoading, showLoader, startLoading, stopLoading } = useLoading();

    useEffect(() => {
        const handleStart = () => startLoading();
        const handleFinish = () => stopLoading();

        document.addEventListener('inertia:start', handleStart);
        document.addEventListener('inertia:finish', handleFinish);

        return () => {
            document.removeEventListener('inertia:start', handleStart);
            document.removeEventListener('inertia:finish', handleFinish);
        };
    }, []);

    return (
        <ErrorBoundary>
            <div className="min-h-screen bg-gray-100">
                <Head title={title} />
                
                <Navigation />

                <main>
                    {showLoader && <PageLoader />}
                    <PageTransition>
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {children}
                        </div>
                    </PageTransition>
                </main>

                <ToastContainer 
                    position="bottom-right"
                    autoClose={5000}
                    hideProgressBar={false}
                    newestOnTop={false}
                    closeOnClick
                    rtl={false}
                    pauseOnFocusLoss
                    draggable
                    pauseOnHover
                    theme="light"
                />
            </div>
        </ErrorBoundary>
    );
} 