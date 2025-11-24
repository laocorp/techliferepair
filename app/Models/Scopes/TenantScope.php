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
        // Si hay un usuario conectado...
        if (Auth::check()) {
            $user = Auth::user();

            // Y NO es el Super Admin (el dueño del SaaS ve todo)
            if (!$user->is_super_admin) {
                // Filtramos TODO por su company_id
                if ($user->company_id) {
                    $builder->where('company_id', $user->company_id);
                } else {
                    // Si no tiene empresa asignada (error de seguridad), no ve nada.
                    $builder->whereRaw('1 = 0');
                }
            }
        }
    }
}
