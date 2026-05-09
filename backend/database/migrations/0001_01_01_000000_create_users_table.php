<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', [
                'producteur',
                'distributeur',
                'consommateur',
                'transporteur',
                'admin_sectoriel',
                'super_administrateur'
            ])->default('consommateur');
            $table->enum('statut', ['actif','en_attente','suspendu'])->default('en_attente');
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};