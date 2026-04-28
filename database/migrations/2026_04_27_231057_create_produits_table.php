<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->decimal('prix', 10, 2);
            $table->integer('quantite');

            $table->enum('status', ['Disponible', 'Indisponible'])
                ->default('Disponible');

            $table->foreignId('sous_categorie_id')
                ->constrained('sous_categories')
                ->onDelete('restrict');

            $table->foreignId('producteur_id')
                ->constrained('producteurs')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};