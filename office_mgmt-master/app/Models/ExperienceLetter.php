<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceLetter extends Model
{
    protected $table = 'experience_letters';

    protected $fillable = [
        'user_id',
        'position',
        'skill',
        'duration',
        'start_date',
        'end_date',
        'issue_date',
        'other',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
