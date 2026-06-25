<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['title', 'amount', 'spent_at', 'category', 'description'];

    protected function casts(): array
    {
        return [
            'spent_at' => 'date',
            'amount' => 'decimal:2',
        ];
    }
}
