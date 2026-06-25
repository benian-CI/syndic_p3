<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulletinDeSolde extends Model
{
    protected $fillable = [
        'villa_id',
        'period_start',
        'period_end',
        'amount',
        'issued_at',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'issued_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}
