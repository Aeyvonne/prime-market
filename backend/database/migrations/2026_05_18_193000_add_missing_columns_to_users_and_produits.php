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
        Schema::table('produits', function (Blueprint $table) {
            if (!Schema::hasColumn('produits', 'photo')) {
                $table->string('photo')->nullable();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'adresse')) {
                $table->string('adresse')->nullable();
            }
            if (!Schema::hasColumn('users', 'statut')) {
                $table->enum('statut', ['actif', 'suspendu', 'en_attente'])->default('en_attente');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            if (Schema::hasColumn('produits', 'photo')) {
                $table->dropColumn('photo');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'adresse')) {
                $table->dropColumn('adresse');
            }
            if (Schema::hasColumn('users', 'statut')) {
                $table->dropColumn('statut');
            }
        });
    }
};
