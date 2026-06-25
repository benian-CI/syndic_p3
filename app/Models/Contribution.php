<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $fillable = [
        'villa_id',
        'month',
        'amount',
        'paid_at',
        'payment_method',
        'reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'paid_at' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}
