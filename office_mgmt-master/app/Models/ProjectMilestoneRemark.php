<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProjectMilestoneRemark extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'milestone_id', 'user_id', 'remark_text', 'remark_type'
    ];
    public function milestone() { return $this->belongsTo(ProjectMilestone::class, 'milestone_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
