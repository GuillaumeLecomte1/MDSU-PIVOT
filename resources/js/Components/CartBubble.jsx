import { Link, usePage } from '@inertiajs/react';

export default function CartBubble() {
    const { cartCount } = usePage().props;

    return (
        <Link 
            href={route('cart.index')} 
            className="relative inline-flex items-center p-2"
        >
            <svg 
                className="h-6 w-6 text-gray-400 hover:text-gray-500" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path 
                    strokeLinecap="round" 
                    strokeLinejoin="round" 
                    strokeWidth="2" 
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" 
                />
            </svg>
            {cartCount > 0 && (
                <span className="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-green-600 flex items-center justify-center text-xs text-white">
                    {cartCount}
                </span>
            )}
        </Link>
    );
} 