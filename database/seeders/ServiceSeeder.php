<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {

        Service::create([

            'user_id' => 2,

            'category_id' => 1,

            'titre' => 'Réparation fuite plomberie',

            'description' => 'Service rapide de plomberie à domicile',

            'tarif' => 15000,

            'photos' => json_encode([]),

            'disponibilite' => true,

            'statut' => 'actif',
        ]);

        Service::create([

            'user_id' => 3,

            'category_id' => 3,

            'titre' => 'Création site web Laravel',

            'description' => 'Développement de site moderne',

            'tarif' => 120000,

            'photos' => json_encode([]),

            'disponibilite' => true,

            'statut' => 'actif',
        ]);
    }
}