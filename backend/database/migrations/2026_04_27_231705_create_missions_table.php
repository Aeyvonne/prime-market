<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();

            $table->enum('status', ['EnCours', 'Valider', 'Annuler'])
                ->default('EnCours');

            $table->decimal('remuneration', 10, 2)->nullable();

            $table->foreignId('transporteur_id')
                ->constrained('transporteurs')
                ->onDelete('restrict');

            $table->foreignId('livraison_id')
                ->unique()
                ->constrained('livraisons')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};