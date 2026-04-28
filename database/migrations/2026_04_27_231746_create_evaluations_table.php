<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();

            $table->integer('note');
            $table->text('commentaire')->nullable();
            $table->dateTime('date')->useCurrent();

            $table->foreignId('evaluateur_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('evalue_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};