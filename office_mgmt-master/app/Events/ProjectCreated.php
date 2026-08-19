<?php
namespace App\Events;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectCreated
{
    use Dispatchable, SerializesModels;
    public $project;
    public $client;
    public $assignedUser;
    public function __construct(Project $project, User $client, User $assignedUser)
    {
        $this->project = $project;
        $this->client = $client;
        $this->assignedUser = $assignedUser;
    }
}
