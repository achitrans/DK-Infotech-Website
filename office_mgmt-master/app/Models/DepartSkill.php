<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartSkill extends Model
{
    protected $table = 'department_skills';

    protected $fillable = [
        'department',
        'skills'
    ];
    protected $casts = [
        'skills' => 'array',
    ];

    public function skills()
    {
        return $this->hasMany(DepartSkill::class);
    }
}
