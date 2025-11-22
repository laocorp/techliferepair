<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Mary\Traits\Toast;
use App\Traits\BelongsToTenant; // <--- IMPORTANTE: Importar el Trait

class Client extends Model
{
    use Toast, BelongsToTenant; // <--- USAR EL TRAIT AQUÍ

    protected $fillable = [
        'name', 'tax_id', 'email', 'phone', 'address', 'city', 'notes', 'company_id'
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    // --- LOGICA DE CREACIÓN DE USUARIO ---
    protected static function booted(): void
    {
        // Primero, llamamos al boot del trait para que se aplique el scope
        static::bootBelongsToTenant();

        static::created(function ($client) {
            
            // 1. Solo creamos usuario si el cliente tiene un EMAIL válido
            if (!empty($client->email)) {
                
                // 2. Verificamos que ese email no esté ya registrado como usuario
                if (!User::where('email', $client->email)->exists()) {
                    
                    // 3. Crear el Usuario automáticamente
                    $user = User::create([
                        'name' => $client->name,
                        'email' => $client->email,
                        'password' => Hash::make('Cliente123'), // <--- CONTRASEÑA TEMPORAL
                        'role' => 'client', // Le damos el rol de cliente (limitado)
                        'client_id' => $client->id, // Lo vinculamos a este perfil de cliente
                        'company_id' => $client->company_id // <--- IMPORTANTE: Vincularlo a la misma empresa
                    ]);
                }
            }
        });
    }
}
