import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import Select from '@/Components/Select';

export default function Create({ categories, ressourceries }) {
    const [selectedImages, setSelectedImages] = useState([]);
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        description: '',
        price: '',
        condition: 'bon état',
        dimensions: '',
        color: '',
        brand: '',
        stock: 1,
        ressourcerie_id: '',
        category_ids: [],
        images: []
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData();
        
        // Ajout des champs texte
        Object.keys(data).forEach(key => {
            if (key !== 'images' && key !== 'category_ids') {
                formData.append(key, data[key]);
            }
        });

        // Ajout des catégories
        data.category_ids.forEach(id => {
            formData.append('category_ids[]', id);
        });

        // Ajout des images
        selectedImages.forEach(image => {
            formData.append('images[]', image);
        });

        post(route('admin.products.store'), formData);
    };

    const handleImageChange = (e) => {
        if (e.target.files) {
            setSelectedImages(Array.from(e.target.files));
        }
    };

    return (
        <AdminLayout>
            <Head title="Créer un produit" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h1 className="text-2xl font-bold mb-6">Créer un nouveau produit</h1>

                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div>
                                    <InputLabel htmlFor="name" value="Nom" />
                                    <TextInput
                                        id="name"
                                        type="text"
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        className="mt-1 block w-full"
                                        required
                                    />
                                    <InputError message={errors.name} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="description" value="Description" />
                                    <textarea
                                        id="description"
                                        value={data.description}
                                        onChange={e => setData('description', e.target.value)}
                                        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                        rows="4"
                                        required
                                    />
                                    <InputError message={errors.description} className="mt-2" />
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel htmlFor="price" value="Prix" />
                                        <TextInput
                                            id="price"
                                            type="number"
                                            step="0.01"
                                            value={data.price}
                                            onChange={e => setData('price', e.target.value)}
                                            className="mt-1 block w-full"
                                            required
                                        />
                                        <InputError message={errors.price} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="stock" value="Stock" />
                                        <TextInput
                                            id="stock"
                                            type="number"
                                            value={data.stock}
                                            onChange={e => setData('stock', e.target.value)}
                                            className="mt-1 block w-full"
                                            required
                                        />
                                        <InputError message={errors.stock} className="mt-2" />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel htmlFor="condition" value="État" />
                                        <Select
                                            id="condition"
                                            value={data.condition}
                                            onChange={e => setData('condition', e.target.value)}
                                            className="mt-1 block w-full"
                                            required
                                        >
                                            <option value="bon état">Bon état</option>
                                            <option value="très bon état">Très bon état</option>
                                            <option value="état moyen">État moyen</option>
                                            <option value="à rénover">À rénover</option>
                                        </Select>
                                        <InputError message={errors.condition} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="dimensions" value="Dimensions" />
                                        <TextInput
                                            id="dimensions"
                                            type="text"
                                            value={data.dimensions}
                                            onChange={e => setData('dimensions', e.target.value)}
                                            className="mt-1 block w-full"
                                        />
                                        <InputError message={errors.dimensions} className="mt-2" />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel htmlFor="color" value="Couleur" />
                                        <TextInput
                                            id="color"
                                            type="text"
                                            value={data.color}
                                            onChange={e => setData('color', e.target.value)}
                                            className="mt-1 block w-full"
                                        />
                                        <InputError message={errors.color} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="brand" value="Marque" />
                                        <TextInput
                                            id="brand"
                                            type="text"
                                            value={data.brand}
                                            onChange={e => setData('brand', e.target.value)}
                                            className="mt-1 block w-full"
                                        />
                                        <InputError message={errors.brand} className="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel htmlFor="ressourcerie_id" value="Ressourcerie" />
                                    <Select
                                        id="ressourcerie_id"
                                        value={data.ressourcerie_id}
                                        onChange={e => setData('ressourcerie_id', e.target.value)}
                                        className="mt-1 block w-full"
                                        required
                                    >
                                        <option value="">Sélectionner une ressourcerie</option>
                                        {ressourceries.map(ressourcerie => (
                                            <option key={ressourcerie.id} value={ressourcerie.id}>
                                                {ressourcerie.name}
                                            </option>
                                        ))}
                                    </Select>
                                    <InputError message={errors.ressourcerie_id} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="category_ids" value="Catégories" />
                                    <div className="mt-2 grid grid-cols-2 md:grid-cols-3 gap-4">
                                        {categories.map(category => (
                                            <label key={category.id} className="inline-flex items-center">
                                                <input
                                                    type="checkbox"
                                                    value={category.id}
                                                    checked={data.category_ids.includes(category.id)}
                                                    onChange={e => {
                                                        const value = parseInt(e.target.value);
                                                        setData('category_ids', 
                                                            e.target.checked
                                                                ? [...data.category_ids, value]
                                                                : data.category_ids.filter(id => id !== value)
                                                        );
                                                    }}
                                                    className="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                                                />
                                                <span className="ml-2">{category.name}</span>
                                            </label>
                                        ))}
                                    </div>
                                    <InputError message={errors.category_ids} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="images" value="Images" />
                                    <input
                                        type="file"
                                        id="images"
                                        onChange={handleImageChange}
                                        multiple
                                        accept="image/*"
                                        className="mt-1 block w-full"
                                    />
                                    <InputError message={errors.images} className="mt-2" />
                                    {selectedImages.length > 0 && (
                                        <div className="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                            {Array.from(selectedImages).map((image, index) => (
                                                <div key={index} className="relative pb-[100%]">
                                                    <img
                                                        src={URL.createObjectURL(image)}
                                                        alt={`Preview ${index + 1}`}
                                                        className="absolute inset-0 w-full h-full object-cover rounded-lg"
                                                    />
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>

                                <div className="flex justify-end">
                                    <PrimaryButton type="submit" disabled={processing}>
                                        Créer le produit
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
} 