<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant; // Importante para SaaS

class Sale extends Model
{
    use BelongsToTenant; 

    protected $fillable = ['company_id', 'user_id', 'total', 'payment_method'];

    // Relación con los items
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Relación con el usuario que vendió
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
