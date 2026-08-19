<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGeoLocationLog extends Model
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'accuracy',
        'distance',
        'is_within_radius',
        'ip_address',
        'device_info',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'accuracy' => 'float',
        'distance' => 'float',
        'is_within_radius' => 'boolean',
    ];

    /**
     * Get the user that owns the geolocation log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
