import React from 'react';
import { Link } from '@inertiajs/react';

export default function ProductCard({ product }) {
    const imageUrl = product.images && product.images[0]
        ? `/storage/products/${product.images[0]}`
        : '/storage/products/product-1-1.jpg';

    return (
        <div className="group relative">
            <div className="aspect-square w-full overflow-hidden rounded-lg bg-gray-200">
                <img
                    src={imageUrl}
                    alt={product.name}
                    className="h-full w-full object-cover object-center group-hover:opacity-75"
                />
                <button className="absolute top-2 right-2 p-2 rounded-full bg-white shadow-md hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-5 h-5">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </button>
            </div>
            <div className="mt-4">
                <h3 className="text-sm text-gray-700">
                    <Link href={route('products.show', product.slug)}>
                        <span aria-hidden="true" className="absolute inset-0" />
                        {product.name}
                    </Link>
                </h3>
                <p className="mt-1 text-sm font-medium text-gray-900">{product.price}€</p>
            </div>
        </div>
    );
} 