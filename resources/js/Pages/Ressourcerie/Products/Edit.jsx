import { Head, useForm } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { useState, useEffect } from 'react';

export default function Edit({ product, categories }) {
    const [previewImages, setPreviewImages] = useState([]);
    const [imagesToDelete, setImagesToDelete] = useState([]);
    const { data, setData, post, processing, errors } = useForm({
        name: product.name,
        description: product.description,
        price: product.price,
        condition: product.condition,
        dimensions: product.dimensions || '',
        color: product.color || '',
        brand: product.brand || '',
        stock: product.stock,
        categories: product.categories.map(cat => cat.id),
        images: [],
        delete_images: [],
        _method: 'PUT'
    });

    useEffect(() => {
        // Afficher les images existantes dans la prévisualisation
        if (product.images) {
            setPreviewImages(product.images.map(image => `/storage/${image.path}`));
        }
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();
        setData('delete_images', imagesToDelete);
        post(route('ressourcerie.products.update', product.id));
    };

    const handleImageChange = (e) => {
        const files = Array.from(e.target.files);
        setData('images', files);

        // Créer des URLs de prévisualisation pour les nouvelles images
        const previews = files.map(file => URL.createObjectURL(file));
        setPreviewImages(previews);
    };

    const handleDeleteImage = (imageId) => {
        setImagesToDelete([...imagesToDelete, imageId]);
    };

    return (
        <MainLayout title="Modifier le produit">
            <Head title="Modifier le produit" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h1 className="text-2xl font-bold">Modifier le produit</h1>
                            </div>

                            <form onSubmit={handleSubmit} className="space-y-6">
                                {/* Nom du produit */}
                                <div>
                                    <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                                        Nom du produit
                                    </label>
                                    <input
                                        type="text"
                                        id="name"
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    />
                                    {errors.name && <div className="text-red-500 text-sm mt-1">{errors.name}</div>}
                                </div>

                                {/* Description */}
                                <div>
                                    <label htmlFor="description" className="block text-sm font-medium text-gray-700">
                                        Description
                                    </label>
                                    <textarea
                                        id="description"
                                        value={data.description}
                                        onChange={e => setData('description', e.target.value)}
                                        rows={4}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    />
                                    {errors.description && <div className="text-red-500 text-sm mt-1">{errors.description}</div>}
                                </div>

                                {/* Prix et Stock */}
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label htmlFor="price" className="block text-sm font-medium text-gray-700">
                                            Prix (€)
                                        </label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            id="price"
                                            value={data.price}
                                            onChange={e => setData('price', e.target.value)}
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                        />
                                        {errors.price && <div className="text-red-500 text-sm mt-1">{errors.price}</div>}
                                    </div>

                                    <div>
                                        <label htmlFor="stock" className="block text-sm font-medium text-gray-700">
                                            Stock
                                        </label>
                                        <input
                                            type="number"
                                            id="stock"
                                            value={data.stock}
                                            onChange={e => setData('stock', e.target.value)}
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                        />
                                        {errors.stock && <div className="text-red-500 text-sm mt-1">{errors.stock}</div>}
                                    </div>
                                </div>

                                {/* État, Dimensions, Couleur, Marque */}
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label htmlFor="condition" className="block text-sm font-medium text-gray-700">
                                            État
                                        </label>
                                        <select
                                            id="condition"
                                            value={data.condition}
                                            onChange={e => setData('condition', e.target.value)}
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                        >
                                            <option value="">Sélectionnez un état</option>
                                            <option value="Neuf">Neuf</option>
                                            <option value="Très bon état">Très bon état</option>
                                            <option value="Bon état">Bon état</option>
                                            <option value="État moyen">État moyen</option>
                                            <option value="À rénover">À rénover</option>
                                        </select>
                                        {errors.condition && <div className="text-red-500 text-sm mt-1">{errors.condition}</div>}
                                    </div>

                                    <div>
                                        <label htmlFor="dimensions" className="block text-sm font-medium text-gray-700">
                                            Dimensions
                                        </label>
                                        <input
                                            type="text"
                                            id="dimensions"
                                            value={data.dimensions}
                                            onChange={e => setData('dimensions', e.target.value)}
                                            placeholder="ex: 100x50x75 cm"
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                        />
                                        {errors.dimensions && <div className="text-red-500 text-sm mt-1">{errors.dimensions}</div>}
                                    </div>

                                    <div>
                                        <label htmlFor="color" className="block text-sm font-medium text-gray-700">
                                            Couleur
                                        </label>
                                        <input
                                            type="text"
                                            id="color"
                                            value={data.color}
                                            onChange={e => setData('color', e.target.value)}
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                        />
                                        {errors.color && <div className="text-red-500 text-sm mt-1">{errors.color}</div>}
                                    </div>

                                    <div>
                                        <label htmlFor="brand" className="block text-sm font-medium text-gray-700">
                                            Marque
                                        </label>
                                        <input
                                            type="text"
                                            id="brand"
                                            value={data.brand}
                                            onChange={e => setData('brand', e.target.value)}
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                        />
                                        {errors.brand && <div className="text-red-500 text-sm mt-1">{errors.brand}</div>}
                                    </div>
                                </div>

                                {/* Catégories */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Catégories
                                    </label>
                                    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        {categories.map((category) => (
                                            <label key={category.id} className="inline-flex items-center">
                                                <input
                                                    type="checkbox"
                                                    value={category.id}
                                                    checked={data.categories.includes(category.id)}
                                                    onChange={(e) => {
                                                        const checked = e.target.checked;
                                                        setData('categories', 
                                                            checked
                                                                ? [...data.categories, category.id]
                                                                : data.categories.filter(id => id !== category.id)
                                                        );
                                                    }}
                                                    className="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                                />
                                                <span className="ml-2">{category.name}</span>
                                            </label>
                                        ))}
                                    </div>
                                    {errors.categories && <div className="text-red-500 text-sm mt-1">{errors.categories}</div>}
                                </div>

                                {/* Images existantes et nouvelles images */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Images actuelles
                                    </label>
                                    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                                        {product.images && product.images.map((image, index) => (
                                            <div key={index} className="relative group">
                                                <img
                                                    src={`/storage/${image.path}`}
                                                    alt={`Image ${index + 1}`}
                                                    className={`h-24 w-24 object-cover rounded-lg ${
                                                        imagesToDelete.includes(image.id) ? 'opacity-50' : ''
                                                    }`}
                                                />
                                                <button
                                                    type="button"
                                                    onClick={() => handleDeleteImage(image.id)}
                                                    className={`absolute top-1 right-1 p-1 rounded-full ${
                                                        imagesToDelete.includes(image.id)
                                                            ? 'bg-red-600 text-white'
                                                            : 'bg-white text-gray-600'
                                                    } shadow-sm opacity-0 group-hover:opacity-100 transition-opacity`}
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        ))}
                                    </div>

                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Ajouter de nouvelles images
                                    </label>
                                    <input
                                        type="file"
                                        multiple
                                        onChange={handleImageChange}
                                        accept="image/*"
                                        className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                                    />
                                    {errors.images && <div className="text-red-500 text-sm mt-1">{errors.images}</div>}

                                    {/* Prévisualisation des nouvelles images */}
                                    {previewImages.length > 0 && (
                                        <div className="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                            {previewImages.map((preview, index) => (
                                                <div key={index} className="relative">
                                                    <img
                                                        src={preview}
                                                        alt={`Preview ${index + 1}`}
                                                        className="h-24 w-24 object-cover rounded-lg"
                                                    />
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>

                                {/* Boutons */}
                                <div className="flex justify-end space-x-4">
                                    <button
                                        type="button"
                                        onClick={() => window.history.back()}
                                        className="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                                    >
                                        Annuler
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50"
                                    >
                                        {processing ? 'Modification en cours...' : 'Enregistrer les modifications'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
} 