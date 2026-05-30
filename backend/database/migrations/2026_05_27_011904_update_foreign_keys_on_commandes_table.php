<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropForeign(['acheteur_id']);
            $table->dropForeign(['vendeur_id']);
        });

        Schema::table('commandes', function (Blueprint $table) {
            $table->foreign('acheteur_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('vendeur_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropForeign(['acheteur_id']);
            $table->dropForeign(['vendeur_id']);
        });

        Schema::table('commandes', function (Blueprint $table) {
            $table->foreign('acheteur_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->foreign('vendeur_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });
    }
};
