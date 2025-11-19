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
    Schema::create('technical_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('repair_order_id')->constrained()->onDelete('cascade');

        // Checklist dinámico (Ej: {"Pantalla": "OK", "Batería": "Falla"})
        $table->json('checklist')->nullable();

        // Fotos (Guardaremos las rutas de los archivos)
        $table->json('photos')->nullable();

        // Textos técnicos
        $table->text('findings'); // Hallazgos
        $table->text('recommendations'); // Recomendaciones

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_reports');
    }
};
