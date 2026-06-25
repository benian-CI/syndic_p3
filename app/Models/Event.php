<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'month',
        'amount',
        'paid_at',
        'payment_method',
        'reference',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'paid_at' => 'date',
            'amount' => 'decimal:2',
        ];
    }
}
