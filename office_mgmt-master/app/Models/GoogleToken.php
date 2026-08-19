<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleToken extends Model
{
    protected $fillable = [
        'user_id',
        'google_email',
        'access_token',
        'refresh_token',
        'expires_in',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
