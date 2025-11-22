<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla de PLANES (Ej: Básico, Premium)
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Ej: "Plan Emprendedor"
            $table->decimal('price', 10, 2);  // Ej: 29.99
            $table->integer('max_users')->default(1); // Límite de usuarios
            $table->integer('max_orders')->default(50); // Límite de órdenes al mes
            $table->timestamps();
        });

        // 2. Tabla de EMPRESAS (Tenants)
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Nombre del Taller
            $table->string('slug')->unique(); // Para la url (taller-pepe) - opcional
            $table->foreignId('plan_id')->constrained(); 
            $table->string('status')->default('active'); // active, suspended
            $table->date('valid_until')->nullable(); // Fecha de corte
            $table->timestamps();
        });

        // 3. Modificar USUARIOS para saber a qué empresa pertenecen
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->boolean('is_super_admin')->default(false)->after('role'); // EL PODER SUPREMO
        });

        // 4. Modificar TODAS las tablas de datos para que tengan dueño (company_id)
        $tables = ['clients', 'assets', 'repair_orders', 'parts', 'technical_reports'];
        
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id'); // No constrained todavía para no romper datos viejos
            });
        }
    }

    public function down(): void
    {
        // Revertir cambios es complejo, simplificamos borrando las tablas nuevas
        Schema::dropIfExists('companies');
        Schema::dropIfExists('plans');
    }
};
