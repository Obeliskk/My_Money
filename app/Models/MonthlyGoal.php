<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyGoal extends Model
{
    protected $fillable = ['month', 'target_amount', 'saved_amount'];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'saved_amount' => 'decimal:2',
        ];
    }
}
