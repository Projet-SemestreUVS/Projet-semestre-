<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {

        Schema::create('users', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | INFORMATIONS UTILISATEUR
            |--------------------------------------------------------------------------
            */

            $table->string('nom');

            $table->string('prenom');

            $table->string('email')->unique();

            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            /*
            |--------------------------------------------------------------------------
            | ROLE
            |--------------------------------------------------------------------------
            */

            $table->enum('role', [

                'admin',

                'prestataire',

                'demandeur'

            ])->default('demandeur');

            /*
            |--------------------------------------------------------------------------
            | INFORMATIONS COMPLEMENTAIRES
            |--------------------------------------------------------------------------
            */

            $table->string('telephone', 20)->nullable(); // CORRECTION: Ajout de la longueur max

            $table->string('photo')->nullable();

            $table->string('localisation', 255)->nullable(); // CORRECTION: Ajout de la longueur max

            /*
            |--------------------------------------------------------------------------
            | TOKEN
            |--------------------------------------------------------------------------
            */

            $table->rememberToken();

            /*
            |--------------------------------------------------------------------------
            | DATES
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
            
            // CORRECTION: Ajout d'index pour les performances
            $table->index(['role']);
            $table->index(['email']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {

        Schema::dropIfExists('users');
    }
};