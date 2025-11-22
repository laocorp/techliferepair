<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToTenant; // <--- Importar el Trait

class Asset extends Model
{
    use BelongsToTenant; // <--- Usar el Trait

    // Campos que permitimos guardar en la BD
    protected $fillable = [
        'client_id', 'brand', 'model', 'serial_number', 'type', 'notes', 'company_id'
    ];

    // Relación: Un equipo pertenece a un Cliente
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
