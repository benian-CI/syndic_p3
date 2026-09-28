<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Street extends Model
{
    protected $fillable = ['name', 'description'];

    public function villas()
    {
        return $this->hasMany(Villa::class);
    }
}
