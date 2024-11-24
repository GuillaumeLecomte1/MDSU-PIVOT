<x-app-layout>  
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mt-6 bg-[#F2F2F2] dark:bg-[#F2F2F2] overflow-hidden shadow-sm sm:rounded-lg">
                @include('components.card.homePage.card-accueil')
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#F2F2F2] dark:bg-[#F2F2F2] ">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                @include('components.card.homePage.card-lien-carte')
                </div>
            </div>
        </div>
    

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#F2F2F2] dark:bg-[#F2F2F2] ">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                @include('components.card.homePage.card-coup-de-coeur')
                </div>
            </div>
        </div>
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mt-6 mb-6 bg-[#F2F2F2] dark:bg-[#F2F2F2] overflow-hidden shadow-sm sm:rounded-lg">
                   @include('components.card.homePage.card-main-categories')
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mt-6 mb-6 bg-[#F2F2F2] dark:bg-[#F2F2F2] overflow-hidden shadow-sm sm:rounded-lg">
                   @include('components.card.homePage.card-dernier-arrivage')
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mt-6 mb-6 bg-[#F2F2F2] dark:bg-[#F2F2F2] overflow-hidden shadow-sm sm:rounded-lg">
                   @include('components.card.homePage.card-qui-somme-nous')
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mt-6 mb-6 bg-[#F2F2F2] dark:bg-[#F2F2F2] overflow-hidden shadow-sm sm:rounded-lg">
                   @include('components.card.homePage.card-blog')
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mt-6 mb-6 bg-[#F2F2F2] dark:bg-[#F2F2F2] overflow-hidden shadow-sm sm:rounded-lg">
                   @include('components.card.homePage.card-newsletters')
        </div>
</x-app-layout>
