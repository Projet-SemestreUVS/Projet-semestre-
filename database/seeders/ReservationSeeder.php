<?php

namespace Database\Seeders;

use App\Models\Reservation;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {

        Reservation::create([

            'service_id' => 1,

            'demandeur_id' => 4,

            'prestataire_id' => 2,

            'date_debut' => now(),

            'statut' => 'en_attente',

            'commentaire' => 'Besoin urgent'
        ]);
    }
}