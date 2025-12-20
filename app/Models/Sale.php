<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Sale extends Model
{
    use BelongsToTenant; 

    protected $fillable = [
        'company_id', 
        'user_id', 
        'client_id', // <--- NUEVO CAMPO
        'total', 
        'payment_method'
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Nueva relación con Cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
