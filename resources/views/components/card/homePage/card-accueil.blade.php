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
        <div class="flex items-center">
            <input type="text" placeholder="Que recherchez-vous ?" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
            <input type="text" placeholder="📍 Angers" class="ml-2 w-1/4 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
            <button class="ml-2 px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4v16m8-16v16M4 8h16m-16 8h16" />
                </svg>
            </button>
        </div>
    </div>
    <div class="md:w-1/2">
        <img src="{{ asset('images/accueilImageCard.png') }}" alt="Image d'accueil" class="w-full h-80 object-cover">
    </div>
</div>
