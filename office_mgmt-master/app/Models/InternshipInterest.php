<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternshipInterest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'branch_id',
        'type',
        'name',
        'email',
        'phone',
        'degree',
        'university',
        'graduation_year',
        'position',
        'start_date_preference',
        'availability_weeks',
        'skills',
        'portfolio_link',
        'github_link',
        'linkedin',
        'resume_file',
        'source',
        'status',
        'notes',
        'consent',
        'ip_address',
        'payment_status',
        'payment_amount',
        'metadata',
        'txn_id',
        'gateway_txn_id',
        'roll_no',
        'college',
        'parent_relation',
        'parent',
        'semester',
        'date_of_joining',
        'start_date',
        'end_date',
        'graduation_course_id',
    ];

    protected $casts = [
        'start_date_preference' => 'date',
        'graduation_year' => 'integer',
        'availability_weeks' => 'integer',
        'consent' => 'boolean',
        'payment_amount' => 'integer',
        'metadata' => 'array',
    ];

    public static $internship_role = [
        'Web Development',
        'Software Development',
        'Digital Marketing',
        'Human Resourse',
        'Mobile Application',
        'Sales And Marketing',
    ];

    public static $sources = [
        'website' => 'Website',
        'referral' => 'Referral',
        'campus' => 'Campus',
        'email' => 'Email',
        'other' => 'Other',
    ];

    public static $statuses = [
        'new' => 'New',
        'reviewed' => 'Reviewed',
        'shortlisted' => 'Shortlisted',
        'interviewed' => 'Interviewed',
        'offered' => 'Offered',
        'rejected' => 'Rejected',
    ];

    public static $types = [
        'internship' => 'Internship',
        'training' => 'Training',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function graduationCourse(): BelongsTo
    {
        return $this->belongsTo(GraduationCourse::class, 'graduation_course_id');
    }
}
