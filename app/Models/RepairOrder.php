<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // <--- Importar esto
use App\Traits\BelongsToTenant;
use Illuminate\Support\Str;

class RepairOrder extends Model
{
    use BelongsToTenant; 

    protected $fillable = [
        'asset_id', 
        'status', 
        'payment_status',
        'problem_description', 
        'diagnosis_notes', 
        'is_warranty', 
        'total_cost',
        'tracking_token',
        'company_id',
        'ticket_number'
    ];

    // ... (tus otras relaciones asset y parts) ...
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

    // 👇👇👇 ESTA ES LA RELACIÓN QUE FALTABA 👇👇👇
    // Una orden tiene un (HasOne) Informe Técnico
    public function technicalReport(): HasOne
    {
        return $this->hasOne(TechnicalReport::class);
    }
    // 👆👆👆 ---------------------------------- 👆👆👆

    // ... (resto de tus accessors y booted) ...
    public function getStatusColorAttribute(): string
    {
        // ... código existente ...
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
        static::bootBelongsToTenant();

        static::creating(function ($order) {
            $order->tracking_token = (string) Str::uuid();

            if (!$order->ticket_number) {
                $company = null;
                if (auth()->check() && auth()->user()->company) {
                    $company = auth()->user()->company;
                } 
                
                if ($company) {
                    $initial = strtoupper(substr($company->name, 0, 1));
                    $count = RepairOrder::where('company_id', $company->id)->count() + 1;
                    $order->ticket_number = $initial . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
                } else {
                    $order->ticket_number = 'G-' . str_pad(RepairOrder::count() + 1, 4, '0', STR_PAD_LEFT);
                }
            }
        });
    }
}
