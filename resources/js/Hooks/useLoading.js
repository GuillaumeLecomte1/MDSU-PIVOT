import { useState, useEffect } from 'react';

export function useLoading(delay = 400) {
    const [isLoading, setIsLoading] = useState(false);
    const [showLoader, setShowLoader] = useState(false);

    useEffect(() => {
        let timeout;
        if (isLoading) {
            timeout = setTimeout(() => {
                setShowLoader(true);
            }, delay);
        } else {
            setShowLoader(false);
        }

        return () => {
            if (timeout) {
                clearTimeout(timeout);
            }
        };
    }, [isLoading, delay]);

    return {
        isLoading,
        showLoader,
        startLoading: () => setIsLoading(true),
        stopLoading: () => setIsLoading(false),
    };
} 