<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Fil d'Ariane -->
        <nav class="text-sm mb-8">
            <ol class="flex items-center">
                <li><a href="#" class="text-gray-500">Catégories</a></li>
                <li class="mx-2">›</li>
                <li><a href="#" class="text-gray-500">Mobilier</a></li>
                <li class="mx-2">›</li>
                <li class="text-black">Chaise</li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Galerie d'images -->
            <div class="md:w-1/4">
                <div class="space-y-4">
                    <div class="border rounded-lg p-2 cursor-pointer">
                        <img src="{{ asset('images/chaise-thumb-1.jpg') }}" alt="Chaise vue 1" class="w-full h-24 object-cover">
                    </div>
                    <div class="border rounded-lg p-2 cursor-pointer">
                        <img src="{{ asset('images/chaise-thumb-2.jpg') }}" alt="Chaise vue 2" class="w-full h-24 object-cover">
                    </div>
                    <div class="border rounded-lg p-2 cursor-pointer">
                        <img src="{{ asset('images/chaise-thumb-3.jpg') }}" alt="Chaise vue 3" class="w-full h-24 object-cover">
                    </div>
                </div>
            </div>

            <!-- Image principale -->
            <div class="md:w-1/2">
                <div class="bg-white rounded-lg overflow-hidden">
                    <img src="{{ asset('images/chaise-main.jpg') }}" alt="Chaise" class="w-full h-[500px] object-cover">
                </div>
            </div>

            <!-- Informations produit -->
            <div class="md:w-1/4">
                <div class="p-6 rounded-lg">
                    <span class="text-green-500 font-bold">MOBILIER</span>
                    <h1 class="text-2xl font-bold mt-2">Chaise</h1>
                    <div class="flex items-center mt-2">
                        <span class="text-sm">Dimensions:</span>
                        <span class="text-sm ml-2">48 x 7 x 43 cm</span>
                    </div>
                    <p class="text-gray-600 mt-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nisi cursus tellus eget fringilla eget. Diam eu est ut leo.</p>
                    <p class="mt-4 text-2xl text-green-500 font-bold">15,00 €</p>
                    <p class="mt-4 text-sm text-gray-500">Vendu par Ressourcerie des biscottes (49)</p>
                    <div class="mt-6 space-y-4">
                        <button class="w-full bg-black text-white py-3 px-4 rounded-lg hover:bg-gray-800 flex items-center justify-center">
                            <span>Ajouter au panier</span>
                            <span class="ml-2">→</span>
                        </button>
                        <a href="#" class="block text-center text-sm text-gray-500 hover:underline">
                            Demander un renseignement
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description détaillée -->
        <div class="mt-12">
            <h2 class="text-xl font-bold mb-4">Description</h2>
            <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nisi cursus tellus eget fringilla eget. Diam eu est ut leo. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nisi cursus tellus eget fringilla eget. Diam eu est ut leo. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nisi cursus tellus eget fringilla eget. Diam eu est ut leo.</p>
        </div>

        <!-- Produits similaires -->
        <div class="mt-12">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">Également dans cette ressourcerie</h2>
                <!-- <a href="#" class="text-white hover:underline flex items-center">
                    tout voir <span class="ml-2">→</span>
                </a> -->
                <button class="w-full bg-black text-white py-3 px-4 rounded-lg hover:bg-gray-800 flex items-center justify-center">
                tout voir <span class="ml-2">→</span>
                </button>
            </div>
            <p class="text-green-500 mb-6">Retrouvez d'autres produits qui pourraient vous plaire dans ce magasin</p>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @for ($i = 0; $i < 4; $i++)
                    <x-products.card-products />
                @endfor
            </div>
        </div>

        <!-- Garanties -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12 bg-green-50 p-8 rounded-lg">
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <!-- Icône "comme neuf" -->
                    </svg>
                </div>
                <h3 class="font-bold mb-2">COMME NEUF</h3>
                <p class="text-sm text-gray-600">Chaque article est contrôlé pour vous garantir un état comme neuf</p>
            </div>
            
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <!-- Icône "paiement sécurisé" -->
                    </svg>
                </div>
                <h3 class="font-bold mb-2">PAIEMENT SÉCURISÉ</h3>
                <p class="text-sm text-gray-600">remboursement garantie en cas d'indisponibilité</p>
            </div>
            
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <!-- Icône "service client" -->
                    </svg>
                </div>
                <h3 class="font-bold mb-2">SERVICE CLIENT</h3>
                <p class="text-sm text-gray-600">Notre service client se tient disponible</p>
            </div>
        </div>
    </div>
</x-app-layout> 