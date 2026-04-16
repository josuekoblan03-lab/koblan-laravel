<?php

use App\Models\Prestation;
use App\Models\User;
use App\Models\ServiceType;
use App\Models\City;

$providers = User::prestataires()->get();
$serviceTypes = ServiceType::all();
$cities = City::all();

if ($providers->count() > 0 && $serviceTypes->count() > 0 && $cities->count() > 0) {
    $newServices = [
        [
            'title' => 'Cuisine Africaine Authentique (Traiteur)',
            'description' => 'Je prépare les meilleurs plats locaux pour vos événements, mariages et fêtes. Garba, Alloco, Attiéké poisson braisé, Kedjenou et bien plus.',
            'price' => 50000,
            'service_type_id' => $serviceTypes->firstWhere('name', 'Cuisinier(ère)')?->id ?? $serviceTypes->random()->id,
        ],
        [
            'title' => 'Installation Électrique Complète',
            'description' => 'Installation de tableaux électriques, dépannage de court-circuit, pose de lustres et câblage complet de votre maison. Sécurité garantie.',
            'price' => 25000,
            'service_type_id' => $serviceTypes->firstWhere('name', 'Électricien')?->id ?? $serviceTypes->random()->id,
        ],
        [
            'title' => 'Nounou Expérimentée pour le weekend',
            'description' => 'Je garde vos enfants le weekend. Activités d\'éveil, repas et bain. Expérience de 5 ans avec les enfants en bas âge.',
            'price' => 15000,
            'service_type_id' => $serviceTypes->firstWhere('name', 'Nounou')?->id ?? $serviceTypes->random()->id,
        ],
        [
            'title' => 'Dépannage Informatique & Réseaux',
            'description' => 'Votre PC est lent ? Problème de connexion Wifi ? J\'interviens rapidement pour résoudre tous vos problèmes informatiques à domicile.',
            'price' => 10000,
            'service_type_id' => $serviceTypes->firstWhere('name', 'Maintenancier info')?->id ?? $serviceTypes->random()->id,
        ],
        [
            'title' => 'Déménagement Complet Sécurisé',
            'description' => 'Nous nous occupons de tout votre déménagement. Emballage, transport sécurisé et déballage. Camion de 20m3 inclus.',
            'price' => 150000,
            'service_type_id' => $serviceTypes->random()->id,
        ],
        [
            'title' => 'Entretien de Jardin et Espaces Verts',
            'description' => 'Tonte de pelouse, taille de haies, désherbage et création de massifs floraux pour embellir votre maison.',
            'price' => 20000,
            'service_type_id' => $serviceTypes->firstWhere('name', 'Jardinier')?->id ?? $serviceTypes->random()->id,
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
            'service_type_id' => $ns['service_type_id'],
            'city_id' => $provider->city_id ?? $cities->random()->id,
            'status' => 'active',
            'views' => rand(10, 100),
        ]);
        echo "Created: {$ns['title']}\n";
    }
}
