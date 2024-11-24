<a href="{{ route('products.show', $product->id ?? '1') }}" class="block hover:shadow-lg transition-shadow duration-200">
    <div class="border rounded-lg shadow-sm overflow-hidden">
        <div class="relative">
            <img src="{{ $product->image_url ?? asset('images/categories/mobilier/chaise.png') }}" 
                 alt="{{ $product->name ?? 'Nom du produit' }}" 
                 class="w-[300px] h-[300px] object-cover bg-white">
            <button class="absolute top-2 right-2 text-gray-500 hover:text-red-500 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                </svg>
            </button>
        </div>
        <div class="p-4">
            <p class="text-gray-500 text-sm">{{ $product->ressourcerie->name ?? 'Ressourcerie' }} ({{ $product->ressourcerie->postal_code ?? '00000' }})</p>
            <p class="text-green-500 font-bold text-sm">{{ strtoupper($product->category->name ?? 'Category'  ) }}</p>
            <div class="flex justify-between items-center">
                <h3 class="text-black font-bold text-lg">{{ $product->name ?? 'Nom du produit' }}</h3>
                <p class="text-gray-500 font-bold text-lg">{{ number_format($product->price ?? '0.00', 2 ) }} €</p>
            </div>
        </div>
    </div>
</a>
