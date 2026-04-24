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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // consommateur
        $table->enum('status', ['en_attente', 'confirmee', 'en_livraison', 'livree', 'annulee'])->default('en_attente');
        $table->decimal('total', 10, 2);
        $table->enum('payment_method', ['wave', 'orange_money', 'cash'])->nullable();
        $table->string('payment_status')->default('non_paye');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
