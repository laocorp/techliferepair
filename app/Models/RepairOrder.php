<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RepairOrder extends Model
{
    protected $fillable = [
        'asset_id', 
        'status', 
        'problem_description', 
        'diagnosis_notes', 
        'is_warranty', 
        'total_cost',
        'tracking_token'    
    ];

    

    // Relación: Una orden pertenece a un Equipo
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // Relación: Una orden tiene muchos repuestos (Tabla Pivote)
    public function parts(): BelongsToMany
    {
        return $this->belongsToMany(Part::class)
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    // Helper visual para los colores de los estados
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'recibido' => 'info',         // Azul
            'diagnostico' => 'warning',   // Amarillo
            'espera_repuestos' => 'error',// Rojo
            'listo' => 'success',         // Verde
            'entregado' => 'neutral',     // Gris
            default => 'primary',
        };
    }

    // Helper para texto bonito del estado
    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }
    
    public function technicalReport()
    {
        return $this->hasOne(TechnicalReport::class);
    } 
    // Generación automática del token al crear
    protected static function booted(): void
    {
        static::creating(function ($order) {
            $order->tracking_token = (string) Str::uuid();
        });
    }
}
