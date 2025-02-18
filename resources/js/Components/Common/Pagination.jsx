import { Link } from '@inertiajs/react';

export default function Pagination({ links }) {
    return (
        <div className="flex flex-wrap justify-center gap-2">
            {links.map((link, key) => {
                if (!link.url) return null;

                return (
                    <Link
                        key={key}
                        href={link.url}
                        className={`px-4 py-2 text-sm rounded-md transition-colors duration-150 ${
                            link.active
                                ? 'bg-emerald-600 text-white'
                                : 'bg-white text-gray-700 hover:bg-emerald-50'
                        }`}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                );
            })}
        </div>
    );
} 