<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    // ESTO ES LO QUE FALTABA 👇
    // Le decimos a Laravel: "Es seguro guardar estos datos"
    protected $fillable = [
        'name',
        'tax_id',
        'email',
        'phone',
        'address',
        'city',
        'notes'
    ];

    // Relación: Un cliente tiene muchos equipos
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
