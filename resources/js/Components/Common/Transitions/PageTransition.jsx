import { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';

export default function PageTransition({ children }) {
    const [mounted, setMounted] = useState(false);
    const { url } = usePage();

    useEffect(() => {
        setMounted(true);
        return () => setMounted(false);
    }, [url]);

    return (
        <div className={`
            transform transition-all duration-300 ease-in-out
            ${mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'}
        `}>
            {children}
        </div>
    );
} 