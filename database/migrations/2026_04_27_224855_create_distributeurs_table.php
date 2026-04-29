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
        Schema::create('distributeurs', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
        
            $table->enum('type_distributeur', ['Grossiste', 'Detaillant']);
            $table->string('adresse')->nullable();
            $table->integer('note')->nullable();
        
            $table->foreign('id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributeurs');
    }
};
