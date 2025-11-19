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
        // UUID es un código largo imposible de adivinar
        $table->uuid('tracking_token')->after('id')->nullable()->unique();
    });
}

public function down(): void
{
    Schema::table('repair_orders', function (Blueprint $table) {
        $table->dropColumn('tracking_token');
    });
}
};
