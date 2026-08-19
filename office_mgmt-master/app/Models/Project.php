<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class Project extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name', 'description', 'department', 'due_date', 'start_date', 'end_date', 'status', 'budget','user_id',
        'client_id','associate_id','tawk_code',
        'branch_id','created_by'
    ];
    public function remarks() { return $this->hasMany(ProjectRemark::class); }
    public function milestones() { return $this->hasMany(ProjectMilestone::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function tasks() { return $this->hasMany(ProjectTask::class); }
    public function client() { return $this->belongsTo(User::class,'client_id','id'); }
    public function associate() { return $this->belongsTo(User::class,'associate_id','id'); }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function statusOptions()
    {
        return [
            'pending' => 'Pending',
            'in progress' => 'In Progress',
            'paused' => 'Paused',
            'completed' => 'Completed',
            'on hold' => 'On Hold',
        ];
    }

    public static function departmentOptions()
    {
        return [
            'development' => 'Development',
            'design' => 'Design',
            'marketing' => 'Marketing',
            'sales' => 'Sales',
            'support' => 'Support',
        ];
    }
}
