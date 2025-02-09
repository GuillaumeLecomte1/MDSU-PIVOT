import React from 'react';
import { Link } from '@inertiajs/react';

export default function BlogCard({ image, title, date, href }) {
    return (
        <Link href={href} className="block group">
            <div className="aspect-video overflow-hidden rounded-lg mb-3">
                <img 
                    src={image} 
                    alt={title}
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
            </div>
            <h3 className="font-medium text-lg mb-1 group-hover:text-green-500 transition-colors">{title}</h3>
            <p className="text-sm text-gray-500">{date}</p>
        </Link>
    );
} 