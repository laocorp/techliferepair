<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    // Campos que permitimos guardar
    protected $fillable = [
        'client_id', 'brand', 'model', 'serial_number', 'type', 'notes'
    ];

    // Relación inversa: Un equipo pertenece a un Cliente
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
