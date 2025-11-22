<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <--- Importante para la relación

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',            // Rol (admin, tech, client)
        'client_id',       // Si es cliente
        'company_id',      // A qué empresa pertenece
        'is_super_admin',  // Si es el dueño del SaaS
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_super_admin' => 'boolean',
    ];

    // --- RELACIONES ---

    // Relación con la Empresa (Tenant)
    // ESTA ES LA FUNCIÓN QUE FALTABA Y CAUSABA EL ERROR
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // --- HELPER METHODS (Funciones de ayuda) ---

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTech(): bool
    {
        return $this->role === 'tech'; // o 'tecnico'
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }
}
