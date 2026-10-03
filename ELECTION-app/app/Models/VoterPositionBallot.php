<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoterPositionBallot extends Model
{
    protected $fillable = [
        'position_id',
        'voter_email',
    ];
}
