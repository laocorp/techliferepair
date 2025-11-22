<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant; // <--- Importar el Trait

class Part extends Model
{
    use BelongsToTenant; // <--- Usar el Trait

    // Esto permite editar estos campos sin bloqueo
    protected $fillable = [
        'name', 
        'sku', 
        'stock', 
        'stock_min', 
        'price', 
        'cost', 
        'location',
        'company_id'
    ];
}
