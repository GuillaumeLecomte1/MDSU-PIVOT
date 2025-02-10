import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';

export default function CsrfToken() {
    const { props } = usePage();

    useEffect(() => {
        if (props.csrf_token) {
            document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', props.csrf_token);
        }
    }, [props.csrf_token]);

    return null;
} 