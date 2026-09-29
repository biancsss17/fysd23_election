<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionPosition extends Model
{
    protected static function booted(): void
    {
        static::creating(function (self $position): void {
            if (! $position->sort_order) {
                $position->sort_order = ((int) static::query()->max('sort_order')) + 1;
            }
        });
    }

    protected $fillable = [
        'name',
        'sort_order',
        'seats',
        'rule',
        'allow_abstain',
        'max_selections',
        'is_completed',
        'is_unlocked',
        'is_closed',
        'candidacy_open',
        'nomination_open',
        'unlocked_at',
    ];

    protected function casts(): array
    {
        return [
            'seats' => 'integer',
            'sort_order' => 'integer',
            'allow_abstain' => 'boolean',
            'max_selections' => 'integer',
            'is_completed' => 'boolean',
            'is_unlocked' => 'boolean',
            'is_closed' => 'boolean',
            'candidacy_open' => 'boolean',
            'nomination_open' => 'boolean',
            'unlocked_at' => 'datetime',
        ];
    }
}
