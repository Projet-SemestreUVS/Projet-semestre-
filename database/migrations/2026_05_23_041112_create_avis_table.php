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
        Schema::create('avis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestataire_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('demandeur_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('reservation_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->integer('note')->min(1)->max(5);
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avis');
    }
};
