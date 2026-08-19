<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GraduationCourse extends Model
{
    protected $table = 'graduation_courses';
    protected $fillable = ['course_name', 'semester'];

    public function internshipInterests()
    {
        return $this->hasMany(InternshipInterest::class);
    }
}
