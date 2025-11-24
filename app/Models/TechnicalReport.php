<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToTenant; // <--- IMPORTANTE: Debe decir App\Traits

class TechnicalReport extends Model
{
    use BelongsToTenant; // <--- Usar el Trait para protección SaaS

    protected $fillable = [
        'repair_order_id', 
        'checklist', 
        'photos', 
        'findings', 
        'recommendations',
        'company_id' // <--- No olvides agregar esto
    ];

    // Casts para JSON
    protected $casts = [
        'checklist' => 'array',
        'photos' => 'array',
    ];

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(RepairOrder::class);
    }
}
