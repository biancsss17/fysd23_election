<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateSubmission extends Model
{
    public function votes()
    {
        return $this->hasMany(ElectionVote::class);
    }

    public function position()
    {
        return $this->belongsTo(ElectionPosition::class);
    }

    protected $fillable = [
        'candidate_name',
        'display_name',
        'submitted_by_email',
        'submission_type',
        'status',
        'is_manual_winner',
        'position_id',
    ];

    protected function casts(): array
    {
        return [
            'is_manual_winner' => 'boolean',
        ];
    }

    public function getDisplayCandidateNameAttribute(): string
    {
        return $this->display_name !== null ? $this->display_name : $this->candidate_name;
    }
}
