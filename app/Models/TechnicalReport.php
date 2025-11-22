<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicalReport extends Model
{
	use BelongsToTenant;
    protected $fillable = ['repair_order_id', 'checklist', 'photos', 'findings', 'recommendations'];

    // Conversión automática de JSON a Array de PHP
    protected $casts = [
        'checklist' => 'array',
        'photos' => 'array',
    ];

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(RepairOrder::class);
    }
}
