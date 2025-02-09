import React from 'react';
import { Link } from '@inertiajs/react';

export default function CategoryCard({ title, icon, color, href }) {
    return (
        <Link href={href} className={`p-6 rounded-lg ${color} hover:opacity-90 transition-opacity`}>
            <div className="flex items-center space-x-3">
                <span className="text-2xl">{icon}</span>
                <h3 className="text-lg font-medium">{title}</h3>
            </div>
        </Link>
    );
} 