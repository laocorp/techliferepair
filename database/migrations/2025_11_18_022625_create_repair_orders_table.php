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
    Schema::create('repair_orders', function (Blueprint $table) {
        $table->id();

        // Relación: La orden pertenece a un EQUIPO (y por ende a un cliente)
        $table->foreignId('asset_id')->constrained()->onDelete('cascade');

        // Estados del taller
        $table->enum('status', ['recibido', 'diagnostico', 'espera_repuestos', 'listo', 'entregado'])->default('recibido');

        // Detalles
        $table->text('problem_description'); // ¿Qué dice el cliente que falla?
        $table->text('diagnosis_notes')->nullable(); // ¿Qué encontró el técnico?
        $table->boolean('is_warranty')->default(false); // ¿Es Garantía?

        // Costos (nullable porque al recibir no sabemos el costo final)
        $table->decimal('total_cost', 10, 2)->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_orders');
    }
};
