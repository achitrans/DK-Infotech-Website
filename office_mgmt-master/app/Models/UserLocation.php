<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    use HasFactory;

    protected $table = 'user_locations';

    protected $fillable = [
        'user_id',
        'recorded_at',
        'latitude',
        'longitude',
        'accuracy_m',
        'altitude_m',
        'speed_mps',
        'heading_deg',
        'source',
        'device_id',
        'ip',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'accuracy_m' => 'float',
        'altitude_m' => 'float',
        'speed_mps' => 'float',
        'heading_deg' => 'integer',
    ];
}
