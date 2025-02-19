import React, { useState } from 'react';
import { toast } from 'react-toastify';

export default function DocLink({ className }) {
    const [isChecking, setIsChecking] = useState(false);
    const DOC_URL = 'http://localhost:5173';

    const checkDocServer = async () => {
        setIsChecking(true);
        
        // Créer un timeout pour la vérification
        const timeoutPromise = new Promise((_, reject) =>
            setTimeout(() => reject(new Error('Timeout')), 1000)
        );

        try {
            // Utiliser fetch avec mode 'no-cors' pour éviter les erreurs CORS
            const fetchPromise = fetch(DOC_URL, { mode: 'no-cors' });
            
            // Race entre le fetch et le timeout
            await Promise.race([fetchPromise, timeoutPromise]);
            
            // Si on arrive ici, le serveur répond
            window.open(DOC_URL, '_blank');
        } catch (error) {
            // Si erreur ou timeout, on considère que le serveur n'est pas démarré
            toast.info(
                <div className="space-y-2">
                    <p className="font-semibold">Le serveur de documentation n'est pas démarré.</p>
                    <div className="bg-gray-50 p-3 rounded-lg">
                        <p className="text-sm text-gray-700">Pour démarrer la documentation :</p>
                        <ol className="list-decimal list-inside mt-2 space-y-1 text-sm">
                            <li>Ouvrez un terminal</li>
                            <li>Naviguez vers le dossier du projet</li>
                            <li>Exécutez la commande :</li>
                        </ol>
                        <code className="bg-gray-100 text-blue-600 p-2 mt-2 block rounded">
                            npm run docs:dev
                        </code>
                    </div>
                    <p className="text-sm text-gray-500 mt-2">
                        Une fois le serveur démarré, cliquez à nouveau sur Documentation.
                    </p>
                </div>,
                {
                    autoClose: false,
                    closeOnClick: true,
                    position: "bottom-right",
                    style: { maxWidth: '400px' }
                }
            );
        } finally {
            setIsChecking(false);
        }
    };

    return (
        <button
            onClick={checkDocServer}
            disabled={isChecking}
            className={`${className} inline-flex items-center ${isChecking ? 'opacity-50 cursor-wait' : 'hover:text-blue-800'}`}
        >
            <span>Documentation</span>
            {isChecking && (
                <svg className="animate-spin ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            )}
        </button>
    );
} 