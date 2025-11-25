<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class CashRegister extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id', 'user_id', 
        'opening_amount', 'opened_at', 
        'closed_at', 'closing_amount', 'calculated_amount', 'difference',
        'status', 'notes'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
