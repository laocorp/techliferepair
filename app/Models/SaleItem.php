<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    // No necesita BelongsToTenant porque depende de Sale que ya lo tiene
    protected $fillable = ['sale_id', 'part_id', 'quantity', 'price'];

    public function part()
    {
        return $this->belongsTo(Part::class);
    }
}
