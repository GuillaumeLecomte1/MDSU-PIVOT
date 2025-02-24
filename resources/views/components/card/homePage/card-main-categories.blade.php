@php
    $categories = [
        [
            'slug' => 'art-de-la-table',
            'name' => 'Art de la table',
            'icon' => '🍽️',
            'color' => 'bg-purple-100 hover:bg-purple-200'
        ],
        [
            'slug' => 'mobilier',
            'name' => 'Mobilier',
            'icon' => '🪑',
            'color' => 'bg-green-100 hover:bg-green-200'
        ],
        [
            'slug' => 'librairie',
            'name' => 'Librairie',
            'icon' => '📚',
            'color' => 'bg-yellow-100 hover:bg-yellow-200'
        ],
        [
            'slug' => 'vetements',
            'name' => 'Vêtements',
            'icon' => '👗',
            'color' => 'bg-orange-100 hover:bg-orange-200'
        ],
        [
            'slug' => 'technologie',
            'name' => 'Technologie',
            'icon' => '🎧',
            'color' => 'bg-gray-100 hover:bg-gray-200'
        ],
        [
            'slug' => 'exterieurs',
            'name' => 'Extérieurs',
            'icon' => '🌿',
            'color' => 'bg-pink-100 hover:bg-pink-200'
        ],
        [
            'slug' => 'loisirs',
            'name' => 'Loisirs',
            'icon' => '🎨',
            'color' => 'bg-teal-100 hover:bg-teal-200'
        ]
    ];
@endphp

<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach($categories as $category)
        <a href="{{ route('categories.show', $category['slug']) }}" class="p-4 rounded-lg flex items-center justify-center {{ $category['color'] }}">
            <div class="text-center">
                <div class="text-2xl">{{ $category['icon'] }}</div>
                <p class="mt-2 font-bold text-black">{{ $category['name'] }}</p>
            </div>
        </a>
    @endforeach

    <a href="{{ route('categories.index') }}" class="bg-blue-100 p-4 rounded-lg flex items-center justify-center hover:bg-blue-200">
        <div class="text-center">
            <div class="text-2xl">📋</div>
            <p class="mt-2 font-bold text-black">Toutes les catégories</p>
        </div>
    </a>
</div>
