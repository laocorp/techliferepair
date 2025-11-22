<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = ['name', 'slug', 'plan_id', 'status', 'valid_until'];

    public function plan(): BelongsTo {
        return $this->belongsTo(Plan::class);
    }

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
    
    public function repairOrders(): HasMany {
        return $this->hasMany(RepairOrder::class);
    }

    // --- LÓGICA DE LÍMITES ---

    // Verificar si puede crear más usuarios
    public function canAddUser(): bool
    {
        // Si no hay plan, asumimos básico (1 usuario)
        $limit = $this->plan->max_users ?? 1;
        $current = $this->users()->count();
        
        return $current < $limit;
    }

    // Verificar si puede crear más órdenes este mes
    public function canCreateOrder(): bool
    {
        $limit = $this->plan->max_orders ?? 50;
        
        // Contamos solo las del mes actual
        $current = $this->repairOrders()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        return $current < $limit;
    }
}
