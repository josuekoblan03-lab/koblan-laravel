<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\Category;
use App\Models\ServiceType;
use App\Models\Region;
use App\Models\Department;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. GÉOGRAPHIE ───────────────────────────────────────────────────
        $regionAbidjan = Region::create(['name' => 'Lagunes']);
        $regionBouake  = Region::create(['name' => 'Vallée du Bandama']);

        $depAbidjan = Department::create(['name' => 'Abidjan', 'region_id' => $regionAbidjan->id]);
        $depBouake  = Department::create(['name' => 'Bouaké',  'region_id' => $regionBouake->id]);

        $abidjan = City::create(['name' => 'Abidjan', 'department_id' => $depAbidjan->id]);
        $bouake  = City::create(['name' => 'Bouaké',  'department_id' => $depBouake->id]);

        $quartiers = [
            ['name' => 'Cocody',            'city_id' => $abidjan->id],
            ['name' => 'Yopougon',          'city_id' => $abidjan->id],
            ['name' => 'Marcory',           'city_id' => $abidjan->id],
            ['name' => 'Plateau',           'city_id' => $abidjan->id],
            ['name' => 'Adjamé',            'city_id' => $abidjan->id],
            ['name' => 'Treichville',       'city_id' => $abidjan->id],
            ['name' => 'Abobo',             'city_id' => $abidjan->id],
            ['name' => 'Quartier Commerce', 'city_id' => $bouake->id],
            ['name' => 'Belleville',        'city_id' => $bouake->id],
        ];
        foreach ($quartiers as $q) {
            Neighborhood::create($q);
        }
        $cocody = Neighborhood::where('name', 'Cocody')->first();

        // ─── 2. CATÉGORIES & SERVICES ───────────────────────────────────────
        $categories = [
            [
                'name' => 'Bricolage & Travaux',
                'icon' => 'fas fa-tools',
                'color' => '#f59e0b',
                'services' => ['Plomberie', 'Électricité', 'Menuiserie', 'Peinture', 'Maçonnerie'],
            ],
            [
                'name' => 'Ménage & Nettoyage',
                'icon' => 'fas fa-broom',
                'color' => '#10b981',
                'services' => ['Nettoyage à domicile', 'Entretien piscine', 'Repassage', 'Grand ménage'],
            ],
            [
                'name' => 'Bien-être & Beauté',
                'icon' => 'fas fa-spa',
                'color' => '#ec4899',
                'services' => ['Coiffure à domicile', 'Massage', 'Maquillage', 'Manucure & Pédicure'],
            ],
            [
                'name' => 'Jardinage & Espaces verts',
                'icon' => 'fas fa-leaf',
                'color' => '#22c55e',
                'services' => ['Taille de haies', 'Entretien gazon', 'Plantation', 'Débroussaillage'],
            ],
            [
                'name' => 'Garde d\'Enfants',
                'icon' => 'fas fa-baby',
                'color' => '#8b5cf6',
                'services' => ['Baby-sitting', 'Garde à domicile', 'Aide aux devoirs', 'Crèche à domicile'],
            ],
            [
                'name' => 'Cuisine & Traiteur',
                'icon' => 'fas fa-utensils',
                'color' => '#f97316',
                'services' => ['Repas à domicile', 'Chef cuisinier', 'Traiteur événement', 'Pâtisserie'],
            ],
            [
                'name' => 'Déménagement & Transport',
                'icon' => 'fas fa-truck',
                'color' => '#3b82f6',
                'services' => ['Déménagement', 'Course & Livraison', 'Transport personnes'],
            ],
            [
                'name' => 'Informatique & Tech',
                'icon' => 'fas fa-laptop',
                'color' => '#06b6d4',
                'services' => ['Réparation PC / Téléphone', 'Installation réseau', 'Création site web', 'Support IT'],
            ],
        ];

        foreach ($categories as $catData) {
            $cat = Category::create([
                'name'  => $catData['name'],
                'icon'  => $catData['icon'],
                'color' => $catData['color'],
            ]);
            foreach ($catData['services'] as $srvName) {
                ServiceType::create([
                    'name'        => $srvName,
                    'category_id' => $cat->id,
                ]);
            }
        }

        // ─── 3. COMPTE ADMIN ────────────────────────────────────────────────
        User::create([
            'name'      => 'Admin KOBLAN',
            'email'     => 'admin@koblan.ci',
            'password'  => Hash::make('12345678'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // ─── 4. COMPTES PRESTATAIRES DE DEMO ET LEURS PRESTATIONS ──────────
        $prestData = [
            ['name' => 'Kouassi Plombier',   'email' => 'plombier@koblan.ci', 'service' => 'Plomberie', 'price' => 15000, 'desc' => 'Dépannage plomberie rapide et efficace.'],
            ['name' => 'Awa Coiffeuse',       'email' => 'coiffeuse@koblan.ci', 'service' => 'Coiffure à domicile', 'price' => 10000, 'desc' => 'Tresses et soins des cheveux.'],
            ['name' => 'Koné Électricien',    'email' => 'electricien@koblan.ci', 'service' => 'Électricité', 'price' => 20000, 'desc' => 'Installation et dépannage électrique.'],
        ];
        foreach ($prestData as $pd) {
            $prest = User::create([
                'name'            => $pd['name'],
                'email'           => $pd['email'],
                'password'        => Hash::make('12345678'),
                'role'            => 'prestataire',
                'city_id'         => $abidjan->id,
                'is_active'       => true,
                'is_verified'     => true,
            ]);
            $prest->wallet()->create(['balance' => 0]);

            // Récupérer l'ID du ServiceType correspondant
            $serviceType = ServiceType::where('name', $pd['service'])->first();
            if ($serviceType) {
                \App\Models\Prestation::create([
                    'user_id'         => $prest->id,
                    'service_type_id' => $serviceType->id,
                    'title'           => $pd['service'] . ' par ' . $pd['name'],
                    'description'     => $pd['desc'],
                    'price'           => $pd['price'],
                    'status'          => 'active',
                ]);
            }
        }

        // ─── 5. COMPTES CLIENTS DE DEMO ─────────────────────────────────────
        $clientData = [
            ['name' => 'Client Demo 1', 'email' => 'client@koblan.ci'],
            ['name' => 'Client Demo 2', 'email' => 'client2@koblan.ci'],
        ];
        foreach ($clientData as $cd) {
            $client = User::create([
                'name'      => $cd['name'],
                'email'     => $cd['email'],
                'password'  => Hash::make('12345678'),
                'role'      => 'client',
                'city_id'   => $abidjan->id,
                'is_active' => true,
            ]);
            $client->wallet()->create(['balance' => 0]);
        }

        $this->command->info('✅ Base de données seedée avec succès !');
        $this->command->info('');
        $this->command->info('🔑 Comptes créés :');
        $this->command->info('   Admin       → admin@koblan.ci       / 12345678');
        $this->command->info('   Prestataire → plombier@koblan.ci    / 12345678');
        $this->command->info('   Client      → client@koblan.ci      / 12345678');
    }
}
