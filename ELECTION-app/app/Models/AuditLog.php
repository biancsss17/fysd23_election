<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'title',
        'action',
        'results',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'results' => 'array',
        ];
    }
}
