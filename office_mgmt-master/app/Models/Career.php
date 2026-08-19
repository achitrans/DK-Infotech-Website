<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Career extends Model
{
    protected $table = 'careers';

    protected $fillable = [
        'name',
        'user_id',
        'interview_id',
        'interview_date',
        'interview_time',
        'interview_type',
        'interview_status',
        'is_joined',
        'joining_on',
        'email',
        'city',
        'mobile',
        'address',
        'department_skills_id',
        'state_id',
        'pincode',
        'photo',
        'resume',
        'office_location',
        'skills',
        'others',
        'status'
    ];

    protected $casts = [
        'skills' => 'array',
        'others' => 'array',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function departmentSkill()
    {
        return $this->belongsTo(DepartSkill::class, 'department_skills_id');
    }


    const TYPE_WALKIN    = 'walk-in';
    const TYPE_SCHEDULED = 'scheduled';
    const TYPE_REMOTE    = 'remote';

    public static function interviewTypes()
    {
        return [
            self::TYPE_WALKIN,
            self::TYPE_SCHEDULED,
            self::TYPE_REMOTE,
        ];
    }

    public function isInterviewScheduled($interview)
    {
        // check interview id hai to regenerate  uniqueness
        if (empty($interview->interview_id)) {

            do {
                $interviewId = 'DKIN-'
                    . strtoupper(Str::slug(explode(' ', $interview->name)[0], '')) . '-'
                    . date('Ymd') . '-'
                    . rand(1000, 9999);
            } while (
                Career::where('interview_id', $interviewId)->exists()
            );

            $interview->interview_id = $interviewId;
        }

        return $interview;
    }
}
