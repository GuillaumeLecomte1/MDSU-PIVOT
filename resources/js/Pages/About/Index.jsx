import AppLayout from '@/Layouts/AppLayout';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, usePage } from '@inertiajs/react';

export default function About() {
    const { auth } = usePage().props;
    const Layout = auth.user ? AppLayout : GuestLayout;

    return (
        <Layout title="Notre Histoire">
            <Head title="Notre Histoire" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h1 className="text-3xl font-bold mb-8">Notre Histoire</h1>
                            
                            <div className="prose max-w-none">
                                <p className="mb-6">
                                    Bienvenue sur notre plateforme dédiée à la seconde vie des objets et à l'économie circulaire.
                                </p>

                                <h2 className="text-2xl font-semibold mt-8 mb-4">Notre Mission</h2>
                                <p className="mb-6">
                                    Notre mission est de faciliter la rencontre entre les ressourceries et les personnes 
                                    souhaitant donner une seconde vie aux objets. Nous croyons en un avenir plus durable 
                                    où la réutilisation et le recyclage sont au cœur de nos habitudes de consommation.
                                </p>

                                <h2 className="text-2xl font-semibold mt-8 mb-4">Nos Valeurs</h2>
                                <ul className="list-disc pl-6 mb-6">
                                    <li className="mb-2">Durabilité et respect de l'environnement</li>
                                    <li className="mb-2">Économie circulaire et locale</li>
                                    <li className="mb-2">Accessibilité et inclusion sociale</li>
                                    <li className="mb-2">Transparence et confiance</li>
                                </ul>

                                <h2 className="text-2xl font-semibold mt-8 mb-4">Notre Impact</h2>
                                <p className="mb-6">
                                    En connectant les ressourceries avec les acheteurs potentiels, nous contribuons à :
                                </p>
                                <ul className="list-disc pl-6 mb-6">
                                    <li className="mb-2">Réduire les déchets et l'impact environnemental</li>
                                    <li className="mb-2">Soutenir l'économie locale et sociale</li>
                                    <li className="mb-2">Créer des emplois durables</li>
                                    <li className="mb-2">Sensibiliser à la consommation responsable</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
} 