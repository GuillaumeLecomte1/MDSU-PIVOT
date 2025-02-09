import { Link } from '@inertiajs/react';

export default function Pagination({ links }) {
    return (
        <div className="flex flex-wrap justify-center gap-2">
            {links.map((link, key) => {
                if (!link.url) {
                    return (
                        <span
                            key={key}
                            className="px-4 py-2 text-gray-500 bg-gray-100 rounded-md"
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    );
                }

                return (
                    <Link
                        key={key}
                        href={link.url}
                        className={`px-4 py-2 rounded-md ${
                            link.active
                                ? 'bg-gray-900 text-white'
                                : 'bg-white text-gray-700 hover:bg-gray-50'
                        }`}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                );
            })}
        </div>
    );
} 