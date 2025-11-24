<?php

namespace App\Traits; // <--- FÍJATE BIEN: El namespace es App\Traits

use App\Models\Scopes\TenantScope;
use App\Models\Company;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        // 1. Al consultar, aplicar el filtro de empresa
        static::addGlobalScope(new TenantScope);

        // 2. Al crear, asignar automáticamente la empresa del usuario
        static::creating(function ($model) {
            if (!$model->company_id && Auth::check() && Auth::user()->company_id) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }

    // Relación estándar para todos los modelos que usen este trait
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
