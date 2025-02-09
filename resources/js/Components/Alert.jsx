import { useState, useEffect } from 'react';

export default function Alert({ message, type = 'success', duration = 3000 }) {
    const [isVisible, setIsVisible] = useState(true);

    useEffect(() => {
        const timer = setTimeout(() => {
            setIsVisible(false);
        }, duration);

        return () => clearTimeout(timer);
    }, [duration]);

    if (!isVisible) return null;

    const bgColor = {
        success: 'bg-green-100 border-green-400 text-green-700',
        error: 'bg-red-100 border-red-400 text-red-700',
        warning: 'bg-yellow-100 border-yellow-400 text-yellow-700',
        info: 'bg-blue-100 border-blue-400 text-blue-700',
    }[type];

    return (
        <div className={`border-l-4 p-4 ${bgColor}`} role="alert">
            <p className="font-medium">{message}</p>
        </div>
    );
} 