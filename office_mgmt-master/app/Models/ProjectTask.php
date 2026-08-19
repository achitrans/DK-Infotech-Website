<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProjectTask extends Model
{
    protected $fillable = [
        'project_id',
        'assigned_to',
        'created_by',
        'updated_by',
        'closed_by',
        'task_name',
        'description',
        'doc_path',
        'start_date',
        'due_date',
        'status',
        'completed_at',
        'started_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'started_at' => 'datetime',
    ];

    public static function statuses()
    {
        return ['pending', 'in progress', 'completed', 'on hold', 'cancelled','closed'];
    }

        public static function statusesPending()
    {
        return ['pending', 'in_progress'];
    }

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function comments()
    {
        return $this->hasMany(ProjectTaskComment::class, 'project_task_id');
    }
}
