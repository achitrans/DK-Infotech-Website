<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{

    protected $fillable = ['name', 'code', 'gst_code'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

}
