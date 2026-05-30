<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE commandes MODIFY COLUMN status ENUM('EnCours', 'Valider', 'Livrer', 'Annuler') NOT NULL DEFAULT 'EnCours'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE commandes MODIFY COLUMN status ENUM('EnCours', 'Valider', 'Annuler') NOT NULL DEFAULT 'EnCours'");
    }
};
