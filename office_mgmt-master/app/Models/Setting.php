<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'name',
        'value',
        'possible_values',
    ];

    protected $casts = [
        'possible_values' => 'array',
    ];
}
