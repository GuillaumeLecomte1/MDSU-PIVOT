import { useState } from 'react';

export default function CategoryFilter({ categories, ressourceries, filters, onFilterChange }) {
    const handleInputChange = (e) => {
        const { name, value, type, checked } = e.target;
        
        let newValue = value;
        if (type === 'checkbox') {
            const categoryIds = [...(filters.categories || [])];
            if (checked) {
                categoryIds.push(value);
            } else {
                const index = categoryIds.indexOf(value);
                if (index > -1) {
                    categoryIds.splice(index, 1);
                }
            }
            newValue = categoryIds;
        }

        const newFilters = {
            ...filters,
            [name]: newValue,
        };

        if ((name === 'min_price' || name === 'max_price') && 
            newFilters.min_price && newFilters.max_price && 
            Number(newFilters.min_price) > Number(newFilters.max_price)) {
            return;
        }

        onFilterChange(newFilters);
    };

    const colors = [
        { name: 'red', hex: '#FF0000' },
        { name: 'blue', hex: '#0000FF' },
        { name: 'black', hex: '#000000' },
        { name: 'yellow', hex: '#FFFF00' }
    ];

    return (
        <div className="w-full lg:w-64 lg:flex-shrink-0">
            <div className="bg-white rounded-lg">
                <h2 className="font-bold text-lg mb-4">FILTRES</h2>
                
                {/* Catégories */}
                <div className="mb-6">
                    <h3 className="font-semibold mb-2 text-sm uppercase">Catégories</h3>
                    <div className="space-y-2">
                        {categories.map((category) => (
                            <label key={category.id} className="flex items-center text-sm cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="categories"
                                    value={category.id}
                                    checked={filters.categories?.includes(category.id.toString())}
                                    onChange={handleInputChange}
                                    className="form-checkbox text-green-500"
                                />
                                <span className="ml-2 text-gray-600">
                                    {category.name}
                                </span>
                            </label>
                        ))}
                    </div>
                </div>

                {/* Prix */}
                <div className="mb-6">
                    <h3 className="font-semibold mb-2 text-sm uppercase">Prix</h3>
                    <div className="flex items-center gap-2">
                        <input
                            type="number"
                            name="min_price"
                            value={filters.min_price || ''}
                            onChange={handleInputChange}
                            className="w-20 p-1 border rounded text-sm"
                            placeholder="0€"
                        />
                        <span>-</span>
                        <input
                            type="number"
                            name="max_price"
                            value={filters.max_price || ''}
                            onChange={handleInputChange}
                            className="w-20 p-1 border rounded text-sm"
                            placeholder="Max €"
                        />
                    </div>
                </div>

                {/* Localisation */}
                <div className="mb-6">
                    <h3 className="font-semibold mb-2 text-sm uppercase">Localisation</h3>
                    <input
                        type="text"
                        name="city"
                        value={filters.city || ''}
                        onChange={handleInputChange}
                        className="w-full p-2 border rounded text-sm"
                        placeholder="Rechercher une ville..."
                    />
                </div>

                {/* Couleurs */}
                <div className="mb-6">
                    <h3 className="font-semibold mb-2 text-sm uppercase">Couleurs</h3>
                    <div className="flex flex-wrap gap-2">
                        {colors.map((color) => (
                            <button
                                key={color.name}
                                onClick={() => handleInputChange({
                                    target: { name: 'color', value: color.name }
                                })}
                                className={`w-6 h-6 rounded-full border ${
                                    filters.color === color.name ? 'ring-2 ring-offset-2 ring-green-500' : ''
                                }`}
                                style={{ backgroundColor: color.hex }}
                            />
                        ))}
                    </div>
                </div>

                {/* Quantité */}
                <div className="mb-6">
                    <h3 className="font-semibold mb-2 text-sm uppercase">Quantité</h3>
                    <div className="space-y-2">
                        <label className="flex items-center text-sm cursor-pointer">
                            <input
                                type="radio"
                                name="quantity"
                                value="all"
                                checked={!filters.quantity || filters.quantity === 'all'}
                                onChange={handleInputChange}
                                className="form-radio text-green-500"
                            />
                            <span className="ml-2 text-gray-600">Tous</span>
                        </label>
                        <label className="flex items-center text-sm cursor-pointer">
                            <input
                                type="radio"
                                name="quantity"
                                value="1-10"
                                checked={filters.quantity === '1-10'}
                                onChange={handleInputChange}
                                className="form-radio text-green-500"
                            />
                            <span className="ml-2 text-gray-600">1-10</span>
                        </label>
                        <label className="flex items-center text-sm cursor-pointer">
                            <input
                                type="radio"
                                name="quantity"
                                value="10+"
                                checked={filters.quantity === '10+'}
                                onChange={handleInputChange}
                                className="form-radio text-green-500"
                            />
                            <span className="ml-2 text-gray-600">10+</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    );
} 