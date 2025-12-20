<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class PartHistory extends Model
{
    use BelongsToTenant;

    protected $fillable = ['company_id', 'part_id', 'user_id', 'action', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
