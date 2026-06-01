<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {

            $table->id();

            $table->foreignId('service_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('demandeur_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('prestataire_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->date('date_debut');

            $table->enum('statut', [
                'en_attente',
                'acceptee',
                'refusee',
                'annulee',
                'terminee'
            ])->default('en_attente');

            $table->text('commentaire')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};