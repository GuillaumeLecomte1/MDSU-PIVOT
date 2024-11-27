<x-app-layout>
    <div class="container-fluid px-4 sm:px-6 lg:px-8 py-8">
        <!-- Navigation des catégories -->
        <div class="w-full mb-8">
            <div class="max-w-full">
                <div class="flex justify-between w-full">
                    @foreach($categories as $category)
                        <a href="{{ route('categories.show', $category->slug) }}" 
                           class="py-4 text-center text-gray-600 transition-colors duration-200 
                               {{ request()->route('category') == $category->slug ? 'font-bold text-gray-900' : '' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filtres -->
            <div class="w-full lg:w-64 lg:flex-shrink-0">
                <div class="bg-white p-6 rounded-lg shadow sticky top-8">
                    <h2 class="font-bold text-lg mb-4">FILTRES</h2>

                    <!-- Catégories -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">CATÉGORIES</h3>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($categories as $category)
                                <label class="flex items-center hover:bg-gray-50 p-2 rounded cursor-pointer">
                                    <input type="checkbox" 
                                           name="categories[]" 
                                           value="{{ $category->id }}"
                                           class="form-checkbox text-indigo-600"
                                           {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}>
                                    <span class="ml-2">{{ $category->name }} 
                                        <span class="text-gray-500 text-sm">({{ $category->products_count }})</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Prix -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">PRIX</h3>
                        <div class="flex items-center gap-2">
                            <input type="number" 
                                   name="min_price" 
                                   value="{{ request('min_price') }}"
                                   class="w-full p-2 border rounded" 
                                   placeholder="Min €">
                            <span>-</span>
                            <input type="number" 
                                   name="max_price"
                                   value="{{ request('max_price') }}"
                                   class="w-full p-2 border rounded" 
                                   placeholder="Max €">
                        </div>
                    </div>

                    <!-- Localisation -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">LOCALISATION</h3>
                        <select name="city" class="w-full p-2 border rounded">
                            <option value="">Toutes les villes</option>
                            @foreach($ressourceries->pluck('city')->unique() as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Grille de produits -->
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <h1 class="text-2xl font-bold">{{ $products->total() }} Produits</h1>
                    <select class="w-full sm:w-auto border rounded p-2" name="sort">
                        <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Trier par : défaut</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Plus récents</option>
                    </select>
                </div>

                @if($products->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500">Aucun produit trouvé</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                        @foreach($products as $product)
                            @php
                                $categoryName = $product->categories->first()?->name ?? 'Non catégorisé';
                                $ressourcerieInfo = $product->ressourcerie ? $product->ressourcerie->name . ' (' . $product->ressourcerie->postal_code . ')' : 'Non assigné';
                                $isAvailable = $product->status === 'available' && $product->quantity > 0;
                                $stockLabel = $isAvailable ? 'En stock' : 'Indisponible';
                                $stockClass = $isAvailable ? 'text-green-600' : 'text-red-600';
                            @endphp
                            
                            <x-products.card-products 
                                :image="$product->images[0] ?? 'images/placeholder.png'"
                                :alt="$product->name ?? 'Produit sans nom'"
                                :ressourcerie="$ressourcerieInfo"
                                :category="$categoryName"
                                :name="$product->name ?? 'Produit sans nom'"
                                :price="number_format($product->price ?? 0, 2) . ' €'"
                                :url="route('products.show', $product->id)"
                                :status="$stockLabel"
                                :statusClass="$stockClass"
                            />
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>


