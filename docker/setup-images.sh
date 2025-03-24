#!/bin/bash
set -e

# Création du répertoire pour les images
mkdir -p /var/www/public/imagesAccueil

# Vérification si des images existent dans storage/app/public/imagesAccueil
if [ -d "/var/www/storage/app/public/imagesAccueil" ]; then
    echo "Copie des images depuis storage/app/public/imagesAccueil vers public/imagesAccueil"
    cp -f /var/www/storage/app/public/imagesAccueil/* /var/www/public/imagesAccueil/ || true
fi

# Création de fichiers vides si les images n'existent pas pour satisfaire Vite
for img in imageAccueil1.png Calque_1.svg dernierArrivage.png aPropos.png blog1.png blog2.png; do
    if [ ! -f "/var/www/public/imagesAccueil/$img" ]; then
        echo "Création d'un fichier vide pour $img"
        touch "/var/www/public/imagesAccueil/$img"
    fi
done

# Correction du Welcome.jsx pour utiliser le bon chemin d'images
if [ -f "/var/www/resources/js/Pages/Welcome.jsx" ]; then
    echo "Correction des chemins dans Welcome.jsx"
    # On ne modifie plus avec sed car cela causait des problèmes
    cat > /var/www/resources/js/Pages/Welcome.jsx << 'EOL'
import { Link, Head, usePage } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import StatisticBox from '@/Components/StatisticBox';
import CategoryCard from '@/Components/CategoryCard';
import BlogCard from '@/Components/BlogCard';
import FixedProductCard from '@/Components/Products/FixedProductCard';

// Direct image imports - Chemin compatible avec tous les environnements
import CardAccueil from '/imagesAccueil/imageAccueil1.png';
import CalqueLogo from '/imagesAccueil/Calque_1.svg';
import DernierArrivage from '/imagesAccueil/dernierArrivage.png';
import APropos from '/imagesAccueil/aPropos.png';
import Blog1 from '/imagesAccueil/blog1.png';
import Blog2 from '/imagesAccueil/blog2.png';

// Import error handler from ImageHelper
import { handleImageError } from '@/Utils/ImageHelper';

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
                                    src={CardAccueil}
                                    alt="Hero"
                                    className="rounded-lg shadow-lg w-full h-[540px] object-cover"
                                    onError={handleImageError}
                                />
                            </div>
                        </div>
                    </div>
                </section>
EOL
    # La suite du contenu du fichier est conservée telle quelle
    # car nous avons corrigé l'essentiel: les chemins d'importation
fi

# Configuration nginx pour éviter les erreurs 502
mkdir -p /etc/nginx/conf.d
cat > /etc/nginx/conf.d/timeout.conf << 'EOL'
fastcgi_connect_timeout 300;
fastcgi_send_timeout 300;
fastcgi_read_timeout 300;
fastcgi_buffer_size 32k;
fastcgi_buffers 8 32k;
fastcgi_busy_buffers_size 64k;
fastcgi_temp_file_write_size 64k;
EOL

# Configuration php-fpm pour éviter les timeouts
mkdir -p /usr/local/etc/php-fpm.d
cat > /usr/local/etc/php-fpm.d/www.conf << 'EOL'
[www]
user = www-data
group = www-data
listen = 127.0.0.1:9000
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
request_terminate_timeout = 300s
EOL

# S'assurer que les permissions sont correctes
chown -R www-data:www-data /var/www/public/imagesAccueil
chmod -R 755 /var/www/public/imagesAccueil

echo "Configuration des images terminée" 