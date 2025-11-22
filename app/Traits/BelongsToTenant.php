<?php

namespace App\Traits;

use App\Models\Scopes\TenantScope;
use App\Models\Company;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        // 1. Aplicar el filtro automático (Global Scope)
        static::addGlobalScope(new TenantScope);

        // 2. Asignar company_id automáticamente al crear
        static::creating(function ($model) {
            // Solo si no se ha especificado ya manualmente un company_id
            if (!$model->company_id) {
                if (Auth::check() && Auth::user()->company_id) {
                    $model->company_id = Auth::user()->company_id;
                }
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
