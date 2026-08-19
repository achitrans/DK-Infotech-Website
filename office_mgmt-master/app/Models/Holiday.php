<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    public static $types = [
        'National' => 'National',
        'Festival' => 'Festival',
        'Optional' => 'Optional',
        'Other' => 'Other',
    ];

    protected $fillable = [
        'date',
        'type',
        'description',
        'is_optional',
    ];

    protected $casts = [
        'date' => 'date',
        'is_optional' => 'boolean',
    ];
}
