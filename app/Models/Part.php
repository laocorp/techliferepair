<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    // Esto permite editar estos campos sin bloqueo
    protected $fillable = [
        'name', 
        'sku', 
        'stock', 
        'stock_min', 
        'price', 
        'cost', 
        'location'
    ];
}
