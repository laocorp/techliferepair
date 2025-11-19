<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash; // <--- Importante
use Illuminate\Support\Str;          // <--- Importante

class Client extends Model
{
    protected $fillable = [
        'name', 'tax_id', 'email', 'phone', 'address', 'city', 'notes'
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    // EVENTO AUTOMÁTICO
    protected static function booted(): void
    {
        static::created(function ($client) {
            // Si tiene email, le creamos un usuario para entrar al portal
            if (!empty($client->email)) {
                // Verificar que el email no exista ya en usuarios
                if (!User::where('email', $client->email)->exists()) {
                    User::create([
                        'name' => $client->name,
                        'email' => $client->email,
                        'password' => Hash::make('Cliente123'), // Contraseña temporal
                        'role' => 'client',
                        'client_id' => $client->id
                    ]);
                }
            }
        });
    }
}
