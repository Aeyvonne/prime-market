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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('acheteur_id')
                ->constrained('users')
                ->onDelete('restrict');
        
            $table->foreignId('vendeur_id')
                ->constrained('users')
                ->onDelete('restrict');
        
            $table->dateTime('date_commande')->useCurrent();
        
            $table->enum('status', ['EnCours', 'Valider', 'Annuler'])
                ->default('EnCours');
        
            $table->decimal('montant_total', 10, 2);
        
            $table->string('mode_livraison')->nullable();
            $table->string('adresse_livraison')->nullable();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
