<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionPosition extends Model
{
    protected $fillable = [
        'name',
        'seats',
        'rule',
        'allow_abstain',
        'max_selections',
        'is_completed',
        'is_unlocked',
        'is_closed',
    ];

    protected function casts(): array
    {
        return [
            'seats' => 'integer',
            'allow_abstain' => 'boolean',
            'max_selections' => 'integer',
            'is_completed' => 'boolean',
            'is_unlocked' => 'boolean',
            'is_closed' => 'boolean',
        ];
    }
}
