<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livraisons', function (Blueprint $table) {
            $table->id();

            $table->string('adresse_livraison');

            $table->enum('status', ['EnCours', 'Valider', 'Annuler'])
                ->default('EnCours');

            $table->date('date_expedition')->nullable();
            $table->date('date_livraison_estimee')->nullable();
            $table->date('date_livraison_reelle')->nullable();

            $table->foreignId('commande_id')
                ->unique()
                ->constrained('commandes')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livraisons');
    }
};