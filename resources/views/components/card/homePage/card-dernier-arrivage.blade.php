<div class="flex flex-col md:flex-row justify-between items-center bg-[#F2F2F2] p-6">
<div class="text-left">
        <h2 class="text-2xl font-bold text-black">Les derniers arrivages</h2>
        <p class="text-green-500 mt-2">Premier arrivé, premier servi !</p>
    </div>
    <button class="mt-4 bg-black text-white py-2 px-4 rounded-lg hover:bg-gray-800">
       dénicher →
    </button>
</div>
    <div class="flex flex-col md:flex-row justify-between items-start">
    <div class="grid grid-cols-2 gap-4">
        <x-products.card-products
            image="images/categories/mobilier/chaise.png"
            alt="Chaise"
            ressourcerie="Ressourcerie des biscottes (49)"
            category="MOBILIER"
            name="Chaise"
            price="15 €"
        />
        <x-products.card-products
            image="images/categories/vetements/jupe.png"
            alt="Jupe plissée"
            ressourcerie="Aigle (49)"
            category="VÊTEMENTS"
            name="Jupe plissée"
            price="7 €"
        />
        <x-products.card-products
            image="images/categories/art-de-la-table/pichet.png"
            alt="Petit pichet à lait"
            ressourcerie="La papoterie (49)"
            category="ART DE LA TABLE"
            name="Petit pichet à lait"
            price="19 €"
        />
        <x-products.card-products
            image="images/categories/mobilier/chaise.png"
            alt="Chaise"
            ressourcerie="Ressourcerie des biscottes (49)"
            category="MOBILIER"
            name="Chaise"
            price="15 €"
        />
    </div>
    <div class="relative mt-8 md:mt-0 md:ml-8">
        <img src="images/dernierarrivage.png" alt="Background Image" class="w-full h-fit object-cover">
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold text-black">Pluie de couleurs</h3>
                <p class="text-green-500 mt-2">Découvrez nos produits colorés dénichés</p>
                <button class="mt-4 bg-black text-white py-2 px-4 rounded-lg hover:bg-gray-800">voir la sélection →</button>
            </div>
        </div>
    </div>
</div>
