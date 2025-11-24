
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            // 1. Eliminamos la regla única global vieja
            // (El nombre suele ser 'tabla_columna_unique')
            $table->dropUnique('parts_sku_unique');

            // 2. Creamos la nueva regla compuesta:
            // "El SKU debe ser único SOLO dentro de la misma company_id"
            $table->unique(['company_id', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'sku']);
            $table->unique('sku');
        });
    }
};
