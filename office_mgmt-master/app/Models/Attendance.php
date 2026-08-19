<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'branch_id', 'attendance_date', 'status', 'in_time', 'out_time', 'working_hours', 'remarks', 'platform'
    ];
    public function user() { return $this->belongsTo(User::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
}
