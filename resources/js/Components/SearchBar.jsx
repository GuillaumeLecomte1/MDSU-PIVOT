import { useState } from 'react';
import { router } from '@inertiajs/react';
import { debounce } from 'lodash';

export default function SearchBar() {
    const [search, setSearch] = useState('');

    const handleSearch = debounce((value) => {
        router.get(route('categories.index'), { search: value }, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 300);

    const handleChange = (e) => {
        const value = e.target.value;
        setSearch(value);
        handleSearch(value);
    };

    return (
        <div className="relative">
            <input
                type="text"
                value={search}
                onChange={handleChange}
                placeholder="Rechercher un produit..."
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-gray-200"
            />
        </div>
    );
} 