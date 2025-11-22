<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla Maestra de Ventas
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Multi-tenant
            $table->foreignId('user_id')->constrained(); // Quién vendió
            $table->decimal('total', 10, 2);
            $table->string('payment_method')->default('cash'); // cash, card, etc.
            $table->timestamps();
        });

        // Tabla de Detalles (Items de la venta)
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('part_id')->constrained();
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // Precio al momento de la venta
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
