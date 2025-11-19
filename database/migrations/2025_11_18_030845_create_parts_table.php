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
    Schema::create('parts', function (Blueprint $table) {
        $table->id();
        $table->string('name');           // Nombre (ej. Filtro de Aire)
        $table->string('sku')->unique();  // Código único (Código de barras)
        $table->integer('stock')->default(0); // Cantidad actual
        $table->integer('stock_min')->default(5); // Alerta de stock bajo
        $table->decimal('price', 10, 2);  // Precio de Venta (al cliente)
        $table->decimal('cost', 10, 2)->nullable(); // Costo de Compra (para tu ganancia)
        $table->text('location')->nullable(); // Ubicación (ej. Estante A, Caja 2)
        $table->timestamps();
    });
}
   
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
