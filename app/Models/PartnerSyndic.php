<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerSyndic extends Model
{
    protected $fillable = [
        'name',
        'city',
        'address',
        'latitude',
        'longitude',
        'login_url',
        'contact_name',
        'contact_email',
        'contact_phone',
        'status',
    ];
}
