<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'        => 'Admin KOBLAN',
                'email'       => 'admin@koblan.ci',
                'password'    => Hash::make('admin123'),
                'role'        => 'admin',
                'phone'       => '+225 07 00 00 00 01',
                'is_verified' => true,
                'is_active'   => true,
                'rating_avg'  => 0,
                'total_reviews' => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Kouamé Jean (Prestataire)',
                'email'       => 'prestataire@koblan.ci',
                'password'    => Hash::make('prestataire123'),
                'role'        => 'prestataire',
                'phone'       => '+225 07 00 00 00 02',
                'bio'         => 'Expert en informatique et electronique. 5 ans d\'expérience à Abidjan.',
                'is_verified' => true,
                'is_active'   => true,
                'rating_avg'  => 4.50,
                'total_reviews' => 12,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Aya Koné (Cliente)',
                'email'       => 'client@koblan.ci',
                'password'    => Hash::make('client123'),
                'role'        => 'client',
                'phone'       => '+225 07 00 00 00 03',
                'is_verified' => true,
                'is_active'   => true,
                'rating_avg'  => 0,
                'total_reviews' => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        foreach ($users as $userData) {
            // Upsert : si l'email existe déjà, on met à jour
            DB::table('users')->updateOrInsert(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ 3 comptes de test créés avec succès !');
        $this->command->table(
            ['Rôle', 'Email', 'Mot de passe'],
            [
                ['👑 Admin',       'admin@koblan.ci',       'admin123'],
                ['🔧 Prestataire', 'prestataire@koblan.ci', 'prestataire123'],
                ['👤 Client',      'client@koblan.ci',      'client123'],
            ]
        );
    }
}
