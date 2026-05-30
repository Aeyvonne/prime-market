<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->string('session_id', 255)->nullable()->after('status');
            $table->string('transaction_id', 255)->nullable()->after('session_id');
        });

        DB::statement("ALTER TABLE paiements MODIFY COLUMN status ENUM('EnCours', 'Valider', 'Annuler', 'Echoué') NOT NULL DEFAULT 'EnCours'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE paiements MODIFY COLUMN status ENUM('EnCours', 'Valider', 'Annuler') NOT NULL DEFAULT 'EnCours'");

        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn(['session_id', 'transaction_id']);
        });
    }
};
