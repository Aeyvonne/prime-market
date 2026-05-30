<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropForeign(['transporteur_id']);
            $table->unsignedBigInteger('transporteur_id')->nullable()->change();
            $table->foreign('transporteur_id')
                ->references('id')
                ->on('transporteurs')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropForeign(['transporteur_id']);
            DB::statement('UPDATE missions SET transporteur_id = 0 WHERE transporteur_id IS NULL');
            $table->unsignedBigInteger('transporteur_id')->nullable(false)->change();
            $table->foreign('transporteur_id')
                ->references('id')
                ->on('transporteurs')
                ->onDelete('restrict');
        });
    }
};
