<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProjectMilestone extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'project_id', 'title', 'description', 'due_date', 'completed_date', 'status', 'order_no'
    ];
    public function project() { return $this->belongsTo(Project::class); }
    public function remarks() { return $this->hasMany(ProjectMilestoneRemark::class, 'milestone_id'); }
}
