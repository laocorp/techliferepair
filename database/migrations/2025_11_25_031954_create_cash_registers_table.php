<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // El cajero
            
            $table->decimal('opening_amount', 10, 2); // Con cuánto inició
            $table->timestamp('opened_at');
            
            $table->timestamp('closed_at')->nullable();
            $table->decimal('closing_amount', 10, 2)->nullable(); // Lo que contó el usuario
            $table->decimal('calculated_amount', 10, 2)->nullable(); // Lo que el sistema dice que debe haber
            $table->decimal('difference', 10, 2)->nullable(); // Diferencia (Sobra/Falta)
            
            $table->string('status')->default('open'); // open, closed
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
