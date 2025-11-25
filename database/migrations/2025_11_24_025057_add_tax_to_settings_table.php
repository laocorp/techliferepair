<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('currency_symbol')->default('$')->after('company_email');
            $table->string('tax_name')->default('IVA')->after('currency_symbol'); // Ej: IVA, VAT
            $table->decimal('tax_rate', 5, 2)->default(0)->after('tax_name'); // Ej: 12.00
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['currency_symbol', 'tax_name', 'tax_rate']);
        });
    }
};
