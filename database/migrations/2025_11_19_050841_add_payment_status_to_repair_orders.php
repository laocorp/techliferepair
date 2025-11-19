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
    Schema::table('repair_orders', function (Blueprint $table) {
        // Por defecto 'pendiente'
        $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending')->after('total_cost');
        // Campo para saber cuánto ha abonado (opcional, para pagos parciales)
        $table->decimal('amount_paid', 10, 2)->default(0)->after('payment_status');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_orders', function (Blueprint $table) {
            //
        });
    }
};
