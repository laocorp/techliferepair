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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            
            // Relación con el Cliente (Dueño)
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            $table->string('brand');          // Marca
            $table->string('model');          // Modelo
            $table->string('serial_number')->unique(); // Serial (Único)
            $table->string('type')->nullable(); // Tipo
            $table->text('notes')->nullable();  // Notas
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
