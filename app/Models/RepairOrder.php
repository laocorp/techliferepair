<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\BelongsToTenant; // <--- IMPORTANTE: ESTA ES LA LÍNEA CORREGIDA
use Illuminate\Support\Str;

class RepairOrder extends Model
{
    use BelongsToTenant; // <--- Aquí activamos la protección

    protected $fillable = [
        'asset_id', 
        'status', 
        'payment_status',
        'problem_description', 
        'diagnosis_notes', 
        'is_warranty', 
        'total_cost',
        'tracking_token',
        'company_id' // Asegúrate de que este campo esté aquí
    ];

    // ... (El resto de tus relaciones y funciones se quedan igual) ...

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function parts(): BelongsToMany
    {
        return $this->belongsToMany(Part::class)
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
    
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'recibido' => 'info',
            'diagnostico' => 'warning',
            'espera_repuestos' => 'error',
            'listo' => 'success',
            'entregado' => 'neutral',
            default => 'primary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getPaymentColorAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => 'error',
            'paid'    => 'success',
            default   => 'neutral',
        };
    }
    
    protected static function booted(): void
    {
        static::creating(function ($order) {
            $order->tracking_token = (string) Str::uuid();
        });
    }
}
