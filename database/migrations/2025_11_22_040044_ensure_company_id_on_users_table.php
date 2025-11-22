<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Solo la crea si no existe
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        // No hacemos nada para no romper datos
    }
};
