import { motion } from 'framer-motion';

export default function PageLoader() {
    return (
        <div className="fixed inset-0 bg-white/75 flex items-center justify-center z-50">
            <div className="flex flex-col items-center gap-4">
                <div className="w-16 h-16 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin" />
                <div className="text-lg font-medium text-gray-700">
                    Chargement...
                </div>
            </div>
        </div>
    );
} 