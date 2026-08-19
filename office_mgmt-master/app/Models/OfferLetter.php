<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferLetter extends Model
{
    protected $fillable = [
        'career_id',
        'position',
        'interview_by_user_id',
        'interview_by_name',
        'ctc',
        'salary',
        'stipend',
        'date_of_joining',
        'created_by',
    ];

    protected $casts = [
        'date_of_joining' => 'date',
        'ctc' => 'decimal:0',
        'salary' => 'decimal:0',
    ];

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interview_by_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
