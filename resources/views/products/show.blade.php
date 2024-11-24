<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Fil d'Ariane -->
        <div class="text-sm breadcrumbs mb-8">
            <span class="text-gray-500">Catalogue</span>
            <span class="mx-2">›</span>
            <span class="text-gray-500">{{ $product->category->name ?? 'Catégorie' }}</span>
            <span class="mx-2">›</span>
            <span class="text-black">{{ $product->name ?? 'Produit' }}</span>
        </div>

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Colonne de gauche : Images -->
            <div class="md:w-2/3">
                <div class="bg-white rounded-lg overflow-hidden mb-4">
                    <img src="{{ $product->image_url ?? asset('images/categories/mobilier/chaise.png') }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-[500px] object-cover">
                </div>
            </div>

            <!-- Colonne de droite : Informations produit -->
            <div class="md:w-1/3">
                <div class="bg-white p-6 rounded-lg">
                    <div class="mb-6">
                        <p class="text-gray-500 text-sm">{{ $product->ressourcerie->name ?? 'Ressourcerie' }} ({{ $product->ressourcerie->postal_code ?? '00000' }})</p>
                        <p class="text-green-500 font-bold text-sm">{{ strtoupper($product->category->name ?? 'Catégorie') }}</p>
                        <h1 class="text-2xl font-bold text-black mt-2">{{ $product->name ?? 'Nom du produit' }}</h1>
                        <p class="text-2xl font-bold text-gray-700 mt-2">{{ number_format($product->price ?? 0, 2) }} €</p>
                    </div>

                    <div class="border-t border-gray-200 py-4">
                        <h2 class="font-semibold mb-2">Description</h2>
                        <p class="text-gray-600">{{ $product->description ?? 'Description du produit non disponible' }}</p>
                    </div>

                    <div class="border-t border-gray-200 py-4">
                        <h2 class="font-semibold mb-2">Dimensions</h2>
                        <p class="text-gray-600">{{ $product->dimensions ?? '44.5 x 47 x 82 cm' }}</p>
                    </div>

                    <div class="border-t border-gray-200 py-4">
                        <h2 class="font-semibold mb-2">État</h2>
                        <p class="text-gray-600">{{ $product->condition ?? 'Bon état' }}</p>
                    </div>

                    <div class="mt-6 space-y-4">
                        <button class="w-full bg-black text-white py-3 px-4 rounded-lg hover:bg-gray-800">
                            Réserver
                        </button>
                        <button class="w-full bg-white text-black border border-black py-3 px-4 rounded-lg hover:bg-gray-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            Ajouter aux favoris
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits similaires -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6">Produits similaires</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @for ($i = 0; $i < 3; $i++)
                    <x-products.card-products :product="$product" />
                @endfor
            </div>
        </div>
    </div>
</x-app-layout> 