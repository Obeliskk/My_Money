<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }
}
