<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            $user = Auth::user();

            // SI ES SUPER ADMIN: NO APLICAR FILTRO (Ve todo, modo Dios)
            if ($user->is_super_admin) {
                return;
            }

            // SI ES MORTAL (Admin o Técnico): Filtrar por su empresa
            if ($user->company_id) {
                $builder->where('company_id', $user->company_id);
            } else {
                // Si no tiene empresa, no ve nada (Seguridad)
                $builder->whereRaw('1 = 0');
            }
        }
    }
}
