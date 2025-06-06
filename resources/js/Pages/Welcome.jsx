import { Link, Head, usePage } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import StatisticBox from '@/Components/StatisticBox';
import CategoryCard from '@/Components/CategoryCard';
import BlogCard from '@/Components/BlogCard';
import FixedProductCard from '@/Components/Products/FixedProductCard';

// Définir les chemins des images
const imageAccueil1 = '/storage/imagesAccueil/imageAccueil1.png';
const calque1 = '/storage/imagesAccueil/Calque_1.svg';
const dernierArrivage = '/storage/imagesAccueil/dernierArrivage.png';
const aPropos = '/storage/imagesAccueil/aPropos.png';
const blog1 = '/storage/imagesAccueil/blog1.png';
const blog2 = '/storage/imagesAccueil/blog2.png';

// Fonction simple pour gérer les erreurs d'images
const handleImageError = (e) => {
    e.target.src = '/storage/default/placeholder.png';
};

export default function Welcome({ latestProducts, popularProducts, categories }) {
    return (
        <MainLayout>
            <Head title="Accueil" />
            <div className="px-24 rounded-lg">
                {/* Hero Section */}
                <section className="py-8 ">
                    <div className="bg-[#E7E7E7] p-12 rounded-lg">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                            <div>
                                <h1 className="text-[64px] leading-tight font-bold mb-6">
                                    La marketplace<br />
                                    des <span className="text-[#FF8D7E]">ressourceries</span><br />
                                    françaises
                                </h1>
                                <p className="text-gray-600 mb-8 text-lg">
                                    Donner une seconde vie aux produits dénichés, ça vaut le coût ! Pivot, est
                                    la première plateforme de click-and-collect dédiée aux ressourceries en
                                    France !
                                </p>
                                <div className="flex space-x-24 mb-12">
                                    <div>
                                        <div className="text-4xl font-bold text-[#4ADE80]">1050+</div>
                                        <div className="text-gray-600">Produits dénichés</div>
                                    </div>
                                    <div>
                                        <div className="text-4xl font-bold text-[#4ADE80]">100+</div>
                                        <div className="text-gray-600">Ressourceries</div>
                                    </div>
                                </div>
                                <div className="relative flex items-center">
                                    <input
                                        type="text"
                                        placeholder="Que recherchez-vous ?"
                                        className="w-full px-6 py-4 rounded-lg border border-gray-200 focus:ring-2 focus:ring-[#4ADE80] focus:border-transparent text-gray-500 placeholder-gray-400"
                                    />
                                    <div className="absolute right-16 top-1/2 transform -translate-y-1/2 border-r border-gray-300 pr-4">
                                        <div className="flex items-center text-gray-400">
                                            <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Angers
                                        </div>
                                    </div>
                                    <button className="absolute right-5 top-1/2 transform -translate-y-1/2">
                                        <svg className="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div className="relative">
                                <img
                                    src={imageAccueil1}
                                    alt="Hero"
                                    className="rounded-lg shadow-lg w-full h-[540px] object-cover"
                                    onError={handleImageError}
                                />
                            </div>
                        </div>
                    </div>
                </section>

                {/* Ressourceries Section */}
                <section className="py-8 ">
                    <div className="flex justify-between items-center mb-4">
                        <div>
                            <h2 className="text-3xl font-bold text-gray-900">Les ressourceries digitalisées</h2>
                            <p className="text-[#4ADE80] mt-2">Retrouvez toutes les annonces des ressourceries près de chez vous !</p>
                        </div>
                        <Link 
                            href="#" 
                            className="inline-flex items-center px-6 py-3 bg-[#14141F] text-white rounded-lg hover:bg-gray-800 transition-colors"
                        >
                            afficher la carte
                            <svg className="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                    </div>
                    <div className="flex flex-wrap justify-between items-center mt-8">
                        {[1, 2, 3, 4, 5].map((i) => (
                            <div key={i} className="w-32 h-32 flex items-center justify-center">
                                <div className="text-4xl font-bold text-gray-200">
                                    <img 
                                        src={calque1} 
                                        alt="Logo" 
                                        className="w-24 h-24 fill-gray-400"
                                        onError={handleImageError}
                                    />
                                </div>
                            </div>
                        ))}
                    </div>
                </section>

                {/* Featured Products */}
                <section className="py-8 ">
                    <div className="grid grid-cols-12 gap-8">
                        <div className="col-span-3">
                            <h2 className="text-[28px] font-bold mb-2">
                                Nos coups<br />
                                de <span className="text-[#FF8D7E]">coeur</span>
                            </h2>
                            <p className="text-gray-500 mb-8">
                                Allongez la durée de
                                vie des objects tout
                                en faisant croître de
                                nouvelles
                                interactions !
                            </p>
                            <Link 
                                href={route('products.index')} 
                                className="inline-flex items-center px-6 py-3 bg-[#14141F] text-white rounded-lg hover:bg-gray-800 transition-colors"
                            >
                                voir le catalogue
                                <svg className="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </Link>
                        </div>
                        <div className="col-span-9 grid grid-cols-3 gap-6">
                            {popularProducts.slice(0, 3).map((product, index) => (
                                <FixedProductCard key={product.id} product={product} index={index} />
                            ))}
                        </div>
                    </div>
                </section>

                {/* Categories Grid */}
                <section className="py-8 ">
                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <CategoryCard
                            title="Art de la table"
                            icon="🍽️"
                            color="bg-pink-100"
                            href={route('categories.show', 'art-de-la-table')}
                        />
                        <CategoryCard
                            title="Mobilier"
                            icon="🪑"
                            color="bg-green-100"
                            href={route('categories.show', 'mobilier')}
                        />
                        <CategoryCard
                            title="Librairie"
                            icon="📚"
                            color="bg-yellow-100"
                            href={route('categories.show', 'librairie')}
                        />
                        <CategoryCard
                            title="Vêtements"
                            icon="👕"
                            color="bg-orange-100"
                            href={route('categories.show', 'vetements')}
                        />
                        <CategoryCard
                            title="Technologie"
                            icon="💻"
                            color="bg-gray-100"
                            href={route('categories.show', 'technologie')}
                        />
                        <CategoryCard
                            title="Extérieur"
                            icon="🌳"
                            color="bg-pink-100"
                            href={route('categories.show', 'exterieur')}
                        />
                        <CategoryCard
                            title="Loisirs"
                            icon="🎨"
                            color="bg-green-100"
                            href={route('categories.show', 'loisirs')}
                        />
                        <CategoryCard
                            title="Toutes les catégories"
                            icon="➡️"
                            color="bg-purple-100"
                            href={route('categories.index')}
                        />
                    </div>
                </section>

                {/* Latest Products */}
                <section className="py-8 ">
                    <div className="flex justify-between items-center mb-6">
                        <div>
                            <h2 className="text-3xl font-bold text-gray-900">Les derniers arrivages</h2>
                            <p className="text-[#4ADE80] mt-2">Premier arrivé, premier servi !</p>
                        </div>
                        <Link 
                            href={route('products.index')} 
                            className="inline-flex items-center px-6 py-3 bg-[#14141F] text-white rounded-lg hover:bg-gray-800 transition-colors"
                        >
                            dénicher
                            <svg className="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                    </div>

                    <div className="grid grid-cols-12 gap-6">
                        {/* Product Grid - 6 columns */}
                        <div className="col-span-6 grid grid-cols-2 gap-6">
                            {latestProducts.slice(0, 4).map((product, index) => (
                                <FixedProductCard key={product.id} product={product} index={index + 3} />
                            ))}
                        </div>

                        {/* Promo Card - 6 columns */}
                        <div className="col-span-6 relative">
                            <div className="h-full rounded-lg overflow-hidden relative">
                                <img 
                                    src={dernierArrivage} 
                                    alt="Pluie de couleurs" 
                                    className="w-full h-full object-cover"
                                    onError={handleImageError}
                                />
                                <div className="absolute inset-0 flex items-center justify-center">
                                    <div className="bg-[#F2F2F2] rounded-lg p-8 mx-8 w-[80%]">
                                        <h3 className="text-[32px] font-bold text-gray-900 mb-3">Pluie de couleurs</h3>
                                        <p className="text-[#4ADE80] text-lg mb-6">Découvrez nos produits colorées déchinées</p>
                                        <Link 
                                            href="#" 
                                            className="inline-flex items-center px-6 py-3 bg-[#14141F] text-white rounded-lg hover:bg-gray-800 transition-colors"
                                        >
                                            voir la sélection
                                            <svg className="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* About Section */}
                <section className="py-12 ">
                    <div className="grid grid-cols-12 gap-24">
                        {/* Left side with image and stats */}
                        <div className="col-span-5 relative">
                            <img
                                src={aPropos}
                                alt="Qui sommes-nous"
                                className="w-full h-[600px] object-cover rounded-lg"
                                onError={handleImageError}
                            />
                            {/* Stats overlaid on image */}
                            <div className="absolute top-[15%] right-[-15%] bg-white rounded-lg p-8 shadow-lg z-10">
                                <div className="text-[64px] font-bold text-[#FF8D7E] leading-none">165</div>
                                <div className="text-gray-600">ressourceries</div>
                            </div>
                            <div className="absolute bottom-[15%] right-[-10%] bg-[#FF8D7E] rounded-lg p-8 shadow-lg">
                                <div className="text-[64px] font-bold text-[#fff] leading-none">28</div>
                                <div className="text-gray-600">infos</div>
                            </div>
                        </div>

                        {/* Right side content */}
                        <div className="col-span-7 pl-12">
                            <h2 className="text-[40px] font-bold text-gray-900 mb-4">Qui sommes-nous ?</h2>
                            <p className="text-[#4ADE80] text-xl mb-8">
                                Notre but est de remettre sur le devant de la scène les produits cachés dans les ressourceries près de chez vous.
                            </p>
                            <p className="text-gray-600 text-lg leading-relaxed mb-8">
                                Pivot a été créé en 2024 pour permettre à chacun d'acheter les plus belles pièces uniques de seconde main. Chaque jour, marchands professionnels proposent sur Pivot leurs meubles vintage, livres, vêtements, tout articles... Les prix affichés sont fixés par ces vendeurs et Pivot opère en tant qu'intermédiaire et tiers de confiance auprès d'eux et des acheteurs. Ces derniers peuvent ainsi dénicher parmi les références de Pivot la perle rare sans bouger de leur canapé. Les pièces proposées à la vente sont quant à elles quotidiennement sélectionnées à la main par nos équipes.
                            </p>
                            <Link 
                                href={route('about')} 
                                className="inline-flex items-center px-6 py-3 bg-[#14141F] text-white rounded-lg hover:bg-gray-800 transition-colors"
                            >
                                en savoir plus
                                <svg className="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </section>

                {/* Blog Section */}
                <section className="py-12 ">
                    <div className="flex justify-between items-start mb-12">
                        <div>
                            <h2 className="text-[40px] font-bold text-gray-900 mb-3">L'actu' ressourcé</h2>
                            <p className="text-[#4ADE80] text-lg">
                                Insertion professionnelles, idées déco, témoignages, arrivages,<br />
                                Pivot aide les ressourceries à communiquer.
                            </p>
                        </div>
                        <Link 
                            href="#" 
                            className="inline-flex items-center px-6 py-3 bg-[#14141F] text-white rounded-lg hover:bg-gray-800 transition-colors"
                        >
                            toutes les actualités
                            <svg className="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                    </div>

                    <div className="grid grid-cols-3 gap-8">
                        {/* First main article */}
                        <div>
                            <img
                                src={blog1}
                                alt="Comment meubler son intérieur"
                                className="w-full aspect-[4/3] object-cover rounded-lg mb-6"
                                onError={handleImageError}
                            />
                            <div className="flex items-center justify-between mb-3">
                                <span className="text-gray-500 text-sm">24/08/2024</span>
                                <Link href="#" className="text-[#4ADE80] text-sm hover:underline inline-flex items-center">
                                    LIRE L'ARTICLE
                                    <svg className="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </Link>
                            </div>
                            <h3 className="text-2xl font-bold">Comment meubler son intérieur avec du seconde main ?</h3>
                        </div>

                        {/* Second main article */}
                        <div>
                            <img
                                src={blog2}
                                alt="Focus ressourcerie"
                                className="w-full aspect-[4/3] object-cover rounded-lg mb-6"
                                onError={handleImageError}
                            />
                            <div className="flex items-center justify-between mb-3">
                                <span className="text-gray-500 text-sm">24/08/2024</span>
                                <Link href="#" className="text-[#4ADE80] text-sm hover:underline inline-flex items-center">
                                    LIRE L'ARTICLE
                                    <svg className="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </Link>
                            </div>
                            <h3 className="text-2xl font-bold">Focus ressourcerie : Rencontre avec des passionnés du réemploi</h3>
                        </div>

                        {/* Right column - small articles */}
                        <div className="space-y-12 ">
                            <div>
                                <div className="flex items-center justify-between mb-3">
                                    <span className="text-gray-500 text-sm">24/08/2024</span>
                                    <Link href="#" className="text-[#4ADE80] text-sm hover:underline inline-flex items-center">
                                        LIRE L'ARTICLE
                                        <svg className="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </Link>
                                </div>
                                <h3 className="text-2xl font-bold">Consommer responsable : Pourquoi acheter d'occasion ?</h3>
                            </div>
                            <div>
                                <div className="flex items-center justify-between mb-3">
                                    <span className="text-gray-500 text-sm">24/08/2024</span>
                                    <Link href="#" className="text-[#4ADE80] text-sm hover:underline inline-flex items-center">
                                        LIRE L'ARTICLE
                                        <svg className="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </Link>
                                </div>
                                <h3 className="text-2xl font-bold">Des meubles vintage et du déco seconde main : Notre sélection du mois</h3>
                            </div>
                            <div>
                                <div className="flex items-center justify-between mb-3">
                                    <span className="text-gray-500 text-sm">24/08/2024</span>
                                    <Link href="#" className="text-[#4ADE80] text-sm hover:underline inline-flex items-center">
                                        LIRE L'ARTICLE
                                        <svg className="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </Link>
                                </div>
                                <h3 className="text-2xl font-bold">Ressourceries et recycleries : Des tremplins vers l'emploi</h3>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Newsletter Section */}
                <section className="py-16 ">
                    <div className="bg-[#F1FBF4] rounded-2xl py-16 px-8">
                        <div className="max-w-3xl mx-auto text-center">
                            <h2 className="text-[40px] font-bold text-gray-900 mb-6">La sélection du mois</h2>
                            <p className="text-[#4ADE80] text-lg mb-12">
                                Pivot rassemble l'ensemble de l'actualité de chaque ressourceries.<br />
                                Inscrivez-vous à notre newsletter pour découvrir les pépites de nos boutiques.
                            </p>
                            <div className="flex gap-3 max-w-2xl mx-auto">
                                <input
                                    type="email"
                                    placeholder="Votre adresse mail"
                                    className="flex-1 px-8 py-4 rounded-lg border border-gray-100 focus:ring-2 focus:ring-[#4ADE80] focus:border-transparent text-gray-400 placeholder-gray-400 text-lg bg-white"
                                />
                                <button className="px-12 py-4 bg-[#14141F] text-white rounded-lg hover:bg-gray-800 transition-colors font-medium text-lg">
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </MainLayout>
    );
}
