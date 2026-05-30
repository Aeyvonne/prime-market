<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents_producteur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producteur_id');
            $table->enum('type', [
                'carte_identite',
                'registre_commerce',
                'certificat_agricole',
                'attestation_terrain',
                'photo_exploitation'
            ]);
            $table->string('fichier');
            $table->enum('statut_verification', ['en_attente', 'verifie', 'rejete'])->default('en_attente');
            $table->text('motif_rejet')->nullable();
            $table->timestamps();

            $table->foreign('producteur_id')
                ->references('id')
                ->on('producteurs')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents_producteur');
    }
};
