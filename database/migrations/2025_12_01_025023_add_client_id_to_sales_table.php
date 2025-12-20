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
    Schema::table('sales', function (Blueprint $table) {
        // Puede ser nulo para ventas de mostrador anónimas
        $table->foreignId('client_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('sales', function (Blueprint $table) {
        $table->dropForeign(['client_id']);
        $table->dropColumn('client_id');
    });
}
};
