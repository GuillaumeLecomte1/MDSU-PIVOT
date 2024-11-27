<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Ressourcerie;
use Illuminate\Database\Seeder;

class RessourcerieSeeder extends Seeder
{
    public function run(): void
    {
        $ressourceries = [
            [
                'name' => 'EcoRecycle Paris',
                'slug' => 'eco-recycle-paris',
                'description' => 'Centre de réemploi et de valorisation des objets du quotidien',
                'address' => '15 rue de la République',
                'city' => 'Paris',
                'postal_code' => '75011',
                'phone' => '0145789632',
                'email' => 'contact@ecorecycle.fr',
                'website_url' => 'https://ecorecycle.fr',
                'opening_hours' => json_encode([
                    'lundi' => '9h00-18h00',
                    'mardi' => '9h00-18h00',
                    'mercredi' => '9h00-18h00',
                    'jeudi' => '9h00-18h00',
                    'vendredi' => '9h00-18h00',
                    'samedi' => '10h00-17h00',
                    'dimanche' => 'fermé'
                ]),
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'siret' => '12345678901234'
            ],
            [
                'name' => 'RecupLyon',
                'slug' => 'recup-lyon',
                'description' => 'Ressourcerie solidaire spécialisée dans le mobilier',
                'address' => '25 rue de la Paix',
                'city' => 'Lyon',
                'postal_code' => '69002',
                'phone' => '0478963214',
                'email' => 'contact@recuplyon.fr',
                'website_url' => 'https://recuplyon.fr',
                'opening_hours' => json_encode([
                    'lundi' => 'fermé',
                    'mardi' => '9h30-18h30',
                    'mercredi' => '9h30-18h30',
                    'jeudi' => '9h30-18h30',
                    'vendredi' => '9h30-18h30',
                    'samedi' => '10h00-19h00',
                    'dimanche' => 'fermé'
                ]),
                'latitude' => 45.7578,
                'longitude' => 4.8320,
                'siret' => '98765432109876'
            ],
            [
                'name' => 'Recyclerie Marseille',
                'slug' => 'recyclerie-marseille',
                'description' => 'Donnons une seconde vie à vos objets',
                'address' => '8 boulevard du Littoral',
                'city' => 'Marseille',
                'postal_code' => '13002',
                'phone' => '0491234567',
                'email' => 'contact@recyclerie-marseille.fr',
                'opening_hours' => json_encode([
                    'lundi' => '10h00-19h00',
                    'mardi' => '10h00-19h00',
                    'mercredi' => '10h00-19h00',
                    'jeudi' => '10h00-19h00',
                    'vendredi' => '10h00-19h00',
                    'samedi' => '9h00-20h00',
                    'dimanche' => '9h00-13h00'
                ]),
                'latitude' => 43.2965,
                'longitude' => 5.3698,
                'siret' => '45678912345678'
            ],
            [
                'name' => 'Ressourcerie Bordelaise',
                'slug' => 'ressourcerie-bordelaise',
                'description' => 'Acteur majeur de l\'économie circulaire à Bordeaux',
                'address' => '45 cours Victor Hugo',
                'city' => 'Bordeaux',
                'postal_code' => '33000',
                'phone' => '0556789123',
                'email' => 'contact@ressourcerie-bordelaise.fr',
                'siret' => '78912345678912'
            ],
            [
                'name' => 'EcoSolidaire Lille',
                'slug' => 'eco-solidaire-lille',
                'description' => 'Ressourcerie sociale et solidaire',
                'address' => '12 rue Nationale',
                'city' => 'Lille',
                'postal_code' => '59000',
                'phone' => '0320123456',
                'email' => 'contact@ecosolidaire-lille.fr',
                'siret' => '32165498732165'
            ],
            [
                'name' => 'Récup\'Nantes',
                'slug' => 'recup-nantes',
                'description' => 'Votre ressourcerie de proximité',
                'address' => '3 rue des Capucins',
                'city' => 'Nantes',
                'postal_code' => '44000',
                'phone' => '0240567891',
                'email' => 'contact@recup-nantes.fr',
                'siret' => '65432198765432'
            ],
            [
                'name' => 'Ressourcerie Toulousaine',
                'slug' => 'ressourcerie-toulousaine',
                'description' => 'Pour un réemploi solidaire',
                'address' => '18 rue des Lois',
                'city' => 'Toulouse',
                'postal_code' => '31000',
                'phone' => '0561234567',
                'email' => 'contact@ressourcerie-toulousaine.fr',
                'siret' => '95175345678912'
            ],
            [
                'name' => 'EcoStrasbourg',
                'slug' => 'eco-strasbourg',
                'description' => 'La ressourcerie alsacienne',
                'address' => '22 rue de la Mésange',
                'city' => 'Strasbourg',
                'postal_code' => '67000',
                'phone' => '0388123456',
                'email' => 'contact@ecostrasbourg.fr',
                'siret' => '36925814736925'
            ],
            [
                'name' => 'Ressourcerie Montpelliéraine',
                'slug' => 'ressourcerie-montpellieraine',
                'description' => 'L\'économie circulaire au cœur de l\'Hérault',
                'address' => '5 rue de la Loge',
                'city' => 'Montpellier',
                'postal_code' => '34000',
                'phone' => '0467891234',
                'email' => 'contact@ressourcerie-montpellier.fr',
                'siret' => '14725836914725'
            ],
            [
                'name' => 'Rennes Récup',
                'slug' => 'rennes-recup',
                'description' => 'La ressourcerie bretonne',
                'address' => '7 rue Saint-Michel',
                'city' => 'Rennes',
                'postal_code' => '35000',
                'phone' => '0299123456',
                'email' => 'contact@rennes-recup.fr',
                'siret' => '25836914725836'
            ]
        ];

        foreach ($ressourceries as $ressourcerie) {
            Ressourcerie::create($ressourcerie);
        }
    }
} 