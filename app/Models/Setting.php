<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant; // <--- FÍJATE QUE DIGA TRAITS, NO MODELS

class Setting extends Model
{
    use BelongsToTenant; // <--- Activar el filtro por empresa

    protected $fillable = [
        'company_name', 
        'company_address', 
        'company_phone', 
        'company_email', 
        'tax_id', 
        'warranty_text',
        'currency_symbol', // <--- Nuevo
        'tax_name',        // <--- Nuevo
        'tax_rate',        // <--- Nuevo
        'company_id'
    ];
}
