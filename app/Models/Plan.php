<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'price', 'max_users', 'max_orders'];
    
    public function companies() {
        return $this->hasMany(Company::class);
    }
}
