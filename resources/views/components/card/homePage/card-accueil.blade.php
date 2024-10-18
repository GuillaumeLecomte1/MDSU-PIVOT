<div class="flex flex-col md:flex-row bg-[#E7E7E7] shadow-lg rounded-lg overflow-hidden">
    <div class="md:w-1/2 p-8 flex flex-col justify-center">
        <h2 class="text-3xl font-bold text-gray-800 dark:text-black mb-4">La marketplace des <span class="text-orange-500">ressourceries</span> françaises</h2>
        <p class="text-gray-600 dark:text-black mb-6">
            Donner une seconde vie aux produits dénichés, ça vaut le coût ! Pivot, est la première plateforme de click-and-collect dédiée aux ressourceries en France !
        </p>
        <div class="flex justify-between mb-6">
            <div class="flex items-center">
                <div class="text-3xl font-bold text-green-500 mr-2">1050+</div>
                <div class="text-gray-600 dark:text-black">Produits dénichés</div>
            </div>
            <div class="w-px h-12 bg-gray-300 dark:bg-gray-600 mx-4"></div>
            <div class="flex items-center">
                <div class="text-3xl font-bold text-green-500 mr-2">100+</div>
                <div class="text-gray-600 dark:text-black">Ressourceries</div>
            </div>
        </div>
        <div class="flex items-center border border-gray-300 rounded-md focus-within:ring-2 focus-within:ring-orange-500">
            <input type="text" placeholder="Que recherchez-vous ? |📍 Angers" class="w-full px-4 py-2 focus:outline-none">
            <button class="px-4 py-2 bg-orange-500 text-white rounded-r-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2v6m0-6V4m0 0L8 6m4-2l2 2" />
                </svg>
            </button>
        </div>
    </div>
    <div class="md:w-1/2">
        <img src="{{ asset('images/accueilImageCard.png') }}" alt="Image d'accueil" class="w-full h-80 object-cover">
    </div>
</div>
