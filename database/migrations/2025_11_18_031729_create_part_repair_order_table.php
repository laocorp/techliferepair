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
    Schema::create('part_repair_order', function (Blueprint $table) {
        $table->id();
        $table->foreignId('repair_order_id')->constrained()->onDelete('cascade');
        $table->foreignId('part_id')->constrained(); // El repuesto
        $table->integer('quantity')->default(1);     // Cuántos usaste
        $table->decimal('price', 10, 2);             // A qué precio se vendió (histórico)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_repair_order');
    }
};
