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
    Schema::table('settings', function (Blueprint $table) {
        // Solo agregamos si no existen
        if (!Schema::hasColumn('settings', 'currency_symbol')) {
            $table->string('currency_symbol')->default('$')->after('company_email');
        }
        if (!Schema::hasColumn('settings', 'tax_name')) {
            $table->string('tax_name')->default('IVA')->after('currency_symbol');
        }
        if (!Schema::hasColumn('settings', 'tax_rate')) {
            $table->decimal('tax_rate', 5, 2)->default(0)->after('tax_name');
        }
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
