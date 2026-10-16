<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodBalance extends Model
{
    protected $fillable = ['period_type', 'period_key', 'amount', 'baseline_net'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'baseline_net' => 'decimal:2',
        ];
    }
}
