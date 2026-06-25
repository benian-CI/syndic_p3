<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Villa extends Model
{
    protected $fillable = [
        'street_id',
        'number',
        'owner_name',
        'owner_email',
        'owner_phone',
        'notes',
    ];

    public function street()
    {
        return $this->belongsTo(Street::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function bulletins()
    {
        return $this->hasMany(BulletinDeSolde::class);
    }
}
