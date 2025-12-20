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
	// Relación con el historial
    public function history()
    {
        return $this->hasMany(PartHistory::class)->latest();
    }
    
    // Helper para registrar cambios
    public function logChange($action, $description)
    {
        $this->history()->create([
            'company_id' => $this->company_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description
        ]);
    }	

}
