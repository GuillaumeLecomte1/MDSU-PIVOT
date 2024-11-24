<div class="flex flex-col md:flex-row justify-between items-center p-6">
    <div class="text-left mr-8 mb-4 md:mb-0">
        <h2 class="text-2xl font-bold text-black">Nos coups de  <span class="text-red-500">coeur</span></h2>
        <p class="text-gray-600 mt-2">Allongez la durée de vie des objets tout en faisant croître de nouvelles interactions !</p>
        <button class="mt-4 bg-black text-white py-2 px-4 rounded-lg hover:bg-gray-800">voir le catalogue →</button>
    </div>
    <div class="flex space-x-4">
        <x-products.card-products
            image="images/categories/mobilier/chaise.png"
            alt="Chaise"
            ressourcerie="Ressourcerie des biscottes (49)"
            category="MOBILIER"
            name="Chaise"
            price="15 €"
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
            image="images/categories/art-de-la-table/assiettes.png"
            alt="Lot d'assiettes"
            ressourcerie="Ressourcerie des biscottes (49)"
            category="ART DE LA TABLE"
            name="Lot d'assiettes"
            price="12 €"
        />
    </div>
</div>
