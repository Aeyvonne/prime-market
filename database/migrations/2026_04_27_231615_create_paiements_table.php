<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();

            $table->enum('methode_paiement', ['Wave', 'OrangeMoney']);

            $table->decimal('montant', 10, 2);

            $table->enum('status', ['EnCours', 'Valider', 'Annuler'])
                ->default('EnCours');

            $table->dateTime('date_transaction')->useCurrent();

            $table->foreignId('commande_id')
                ->unique()
                ->constrained('commandes')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};