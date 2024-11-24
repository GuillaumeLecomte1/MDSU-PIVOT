<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Navigation des catégories -->
        <div class="flex overflow-x-auto space-x-6 mb-8">
            <a href="#" class="text-gray-600 hover:text-gray-900 whitespace-nowrap">Nouveautés</a>
            <a href="#" class="text-gray-600 hover:text-gray-900 whitespace-nowrap">Art de la table</a>
            <a href="#" class="text-gray-600 hover:text-gray-900 whitespace-nowrap">Mobilier</a>
            <a href="#" class="text-gray-600 hover:text-gray-900 whitespace-nowrap">Librairie</a>
            <a href="#" class="text-gray-600 hover:text-gray-900 whitespace-nowrap">Vêtements</a>
            <a href="#" class="text-gray-600 hover:text-gray-900 whitespace-nowrap">Technologie</a>
            <a href="#" class="text-gray-600 hover:text-gray-900 whitespace-nowrap">Extérieur</a>
            <a href="#" class="text-gray-600 hover:text-gray-900 whitespace-nowrap">Loisirs</a>
        </div>

        <div class="flex gap-8">
            <!-- Filtres -->
            <div class="w-64 flex-shrink-0">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="font-bold text-lg mb-4">FILTRES</h2>

                    <!-- Catégories -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">CATÉGORIES</h3>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox">
                                <span class="ml-2">Art de la table (87)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox">
                                <span class="ml-2">Mobilier (67)</span>
                            </label>
                            <!-- Ajoutez les autres catégories -->
                        </div>
                    </div>

                    <!-- Prix -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">PRIX</h3>
                        <div class="flex items-center gap-2">
                            <input type="number" class="w-20 p-1 border rounded" placeholder="10 €">
                            <span>-</span>
                            <input type="number" class="w-20 p-1 border rounded" placeholder="150 €">
                        </div>
                    </div>

                    <!-- Localisation -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">LOCALISATION</h3>
                        <input type="text" class="w-full p-2 border rounded" placeholder="Rechercher">
                    </div>

                    <!-- Couleurs -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">COULEURS</h3>
                        <div class="flex gap-2">
                            <button class="w-6 h-6 rounded-full bg-green-500"></button>
                            <button class="w-6 h-6 rounded-full bg-red-500"></button>
                            <button class="w-6 h-6 rounded-full bg-yellow-500"></button>
                            <button class="w-6 h-6 rounded-full bg-black"></button>
                            <button class="w-6 h-6 rounded-full bg-orange-500"></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grille de produits -->
            <div class="flex-1">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Produits</h1>
                    <select class="border rounded p-2">
                        <option>Trier par : défaut</option>
                        <option>Prix croissant</option>
                        <option>Prix décroissant</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Utilisation du composant card-products -->
                    @for ($i = 0; $i < 9; $i++)
                        <x-products.card-products 
                            image="images/categories/mobilier/chaise.png"
                            alt="Chaise"
                            ressourcerie="Ressourcerie des biscottes (49)"
                            category="MOBILIER"
                            name="Chaise"
                            price="15 €"
                        />
                    @endfor
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-8 gap-2">
                    <a href="#" class="px-3 py-1 border rounded">1</a>
                    <a href="#" class="px-3 py-1 border rounded">2</a>
                    <a href="#" class="px-3 py-1 border rounded">3</a>
                    <span>...</span>
                    <a href="#" class="px-3 py-1 border rounded">10</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
