import { router } from '@inertiajs/react';
import { useState } from 'react';

export default function ActionButton({ route, method = 'post', children, className = '', onClick = null }) {
    const [isLoading, setIsLoading] = useState(false);

    const handleClick = () => {
        if (isLoading) return;
        
        setIsLoading(true);
        
        if (onClick) {
            onClick();
        }

        router[method](route, {}, {
            preserveScroll: true,
            onFinish: () => setIsLoading(false),
        });
    };

    return (
        <button
            onClick={handleClick}
            disabled={isLoading}
            className={`transition-colors duration-200 ${isLoading ? 'opacity-75 cursor-not-allowed' : ''} ${className}`}
        >
            {children}
        </button>
    );
} 