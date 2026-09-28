<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionVote extends Model
{
    protected $fillable = [
        'position_id',
        'candidate_submission_id',
        'voter_email',
        'is_abstain',
    ];

    protected function casts(): array
    {
        return ['is_abstain' => 'boolean'];
    }
}
