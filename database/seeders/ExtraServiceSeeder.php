<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prestation;
use App\Models\User;
use App\Models\ServiceType;
use App\Models\City;

class ExtraServiceSeeder extends Seeder
{
    public function run()
    {
        $providers = User::prestataires()->get();
        $serviceTypes = ServiceType::all();
        $cities = City::all();

        if ($providers->count() > 0 && $serviceTypes->count() > 0 && $cities->count() > 0) {
            $newServices = [
                [
                    'title' => 'Cuisine Africaine Authentique (Traiteur)',
                    'description' => 'Je prépare les meilleurs plats locaux pour vos événements, mariages et fêtes. Garba, Alloco, Attiéké poisson braisé, Kedjenou et bien plus.',
                    'price' => 50000,
                    'service_type_name' => 'Cuisine Africaine',
                ],
                [
                    'title' => 'Installation Électrique Complète',
                    'description' => 'Installation de tableaux électriques, dépannage de court-circuit, pose de lustres et câblage complet de votre maison. Sécurité garantie.',
                    'price' => 25000,
                    'service_type_name' => 'Électricité',
                ],
                [
                    'title' => 'Nounou Expérimentée pour le weekend',
                    'description' => 'Je garde vos enfants le weekend. Activités d\'éveil, repas et bain. Expérience de 5 ans avec les enfants en bas âge.',
                    'price' => 15000,
                    'service_type_name' => 'Garde d\'enfants',
                ],
                [
                    'title' => 'Dépannage Informatique & Réseaux',
                    'description' => 'Votre PC est lent ? Problème de connexion Wifi ? J\'interviens rapidement pour résoudre tous vos problèmes informatiques à domicile.',
                    'price' => 10000,
                    'service_type_name' => 'Informatique',
                ],
                [
                    'title' => 'Déménagement Complet Sécurisé',
                    'description' => 'Nous nous occupons de tout votre déménagement. Emballage, transport sécurisé et déballage. Camion de 20m3 inclus.',
                    'price' => 150000,
                    'service_type_name' => 'Déménagement',
                ],
                [
                    'title' => 'Entretien de Jardin et Espaces Verts',
                    'description' => 'Tonte de pelouse, taille de haies, désherbage et création de massifs floraux pour embellir votre maison.',
                    'price' => 20000,
                    'service_type_name' => 'Jardinage',
                ],
            ];

            foreach ($newServices as $ns) {
                $provider = $providers->random();
                
                Prestation::create([
                    'title' => $ns['title'],
                    'description' => $ns['description'],
                    'price' => $ns['price'],
                    'price_type' => 'fixed',
                    'user_id' => $provider->id,
                    'service_type_id' => $serviceTypes->random()->id,
                    'city_id' => $provider->city_id ?? $cities->random()->id,
                    'status' => 'active',
                    'views' => rand(10, 100),
                ]);
            }
        }
    }
}
