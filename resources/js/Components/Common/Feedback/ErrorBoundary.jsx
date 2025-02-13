import React from 'react';
import { toast } from 'react-toastify';

class ErrorBoundary extends React.Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false, error: null };
    }

    static getDerivedStateFromError(error) {
        return { hasError: true, error };
    }

    componentDidCatch(error, errorInfo) {
        console.error('Error caught by boundary:', error, errorInfo);
        toast.error('Une erreur est survenue. Veuillez réessayer.');
    }

    render() {
        if (this.state.hasError) {
            return (
                <div className="min-h-screen bg-gray-100 flex flex-col justify-center items-center">
                    <div className="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
                        <h2 className="text-2xl font-bold text-red-600 mb-4">
                            Oups ! Quelque chose s'est mal passé
                        </h2>
                        <p className="text-gray-600 mb-6">
                            Nous sommes désolés, mais une erreur est survenue. Veuillez rafraîchir la page ou réessayer plus tard.
                        </p>
                        <button
                            onClick={() => window.location.reload()}
                            className="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition-colors"
                        >
                            Rafraîchir la page
                        </button>
                    </div>
                </div>
            );
        }

        return this.props.children;
    }
}

export default ErrorBoundary; 