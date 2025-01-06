import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import ProductCard from '@/Components/Products/ProductCard';
import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';

export default function Show({ ressourcerie, products }) {
    return (
        <AppLayout title={ressourcerie.name}>
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div className="p-6">
                            <h1 className="text-3xl font-bold mb-4">{ressourcerie.name}</h1>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <p className="text-gray-600 mb-4">{ressourcerie.description}</p>
                                    <div className="space-y-2 text-gray-600">
                                        <p><strong>Adresse :</strong> {ressourcerie.address}</p>
                                        <p><strong>Ville :</strong> {ressourcerie.city}</p>
                                        <p><strong>Téléphone :</strong> {ressourcerie.phone}</p>
                                        <p><strong>Email :</strong> {ressourcerie.email}</p>
                                    </div>
                                </div>
                                <div className="h-64">
                                    <MapContainer
                                        center={[ressourcerie.latitude, ressourcerie.longitude]}
                                        zoom={13}
                                        className="h-full w-full rounded-lg"
                                    >
                                        <TileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" />
                                        <Marker position={[ressourcerie.latitude, ressourcerie.longitude]}>
                                            <Popup>{ressourcerie.name}</Popup>
                                        </Marker>
                                    </MapContainer>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h2 className="text-2xl font-bold mb-6">Produits disponibles</h2>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                {products.data.map((product) => (
                                    <ProductCard key={product.id} product={product} />
                                ))}
                            </div>
                            <div className="mt-6">
                                {/* Pagination component here */}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
} 