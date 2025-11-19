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
	    Schema::create('clients', function (Blueprint $table) {
	        $table->id();
	        $table->string('name');               // Nombre o Razón Social
	        $table->string('tax_id')->nullable(); // RUC, DNI, Cédula (Vital para facturas)
	        $table->string('email')->nullable();  // Correo
	        $table->string('phone')->nullable();  // Celular (Para WhatsApp)
	        $table->text('address')->nullable();  // Dirección
	        $table->text('notes')->nullable();    // Notas internas (ej. "Cliente VIP")
	        $table->timestamps();
	    });
	}
	
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
