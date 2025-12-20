<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('part_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('part_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // Quién hizo el cambio
            
            $table->string('action'); // 'creacion', 'actualizacion', 'venta', 'ajuste'
            $table->text('description'); // Ej: "Stock cambió de 5 a 10"
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_histories');
    }
};
