<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_orders', function (Blueprint $table) {
            // Agregamos la columna ticket_number
            // La ponemos nullable por ahora para no romper los datos viejos
            $table->string('ticket_number')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('repair_orders', function (Blueprint $table) {
            $table->dropColumn('ticket_number');
        });
    }
};
