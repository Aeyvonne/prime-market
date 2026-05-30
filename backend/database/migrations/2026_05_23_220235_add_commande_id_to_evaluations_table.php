<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->foreignId('commande_id')
                ->nullable()
                ->constrained('commandes')
                ->onDelete('cascade')
                ->after('evalue_id');

            $table->unique(['evaluateur_id', 'evalue_id', 'commande_id'], 'evaluations_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropUnique('evaluations_unique_idx');
            $table->dropForeign(['commande_id']);
            $table->dropColumn('commande_id');
        });
    }
};
