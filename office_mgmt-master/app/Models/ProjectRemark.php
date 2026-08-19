<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProjectRemark extends Model
{
    use SoftDeletes;
    protected $table = 'project_remarks';
    protected $fillable = [
        'project_id', 'user_id', 'remark_text', 'remark_type'
    ];

    public function project() { return $this->belongsTo(Project::class); }
    public function user() { return $this->belongsTo(User::class); }
}
