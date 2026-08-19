<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Project;
use App\Models\User;

class ProjectNotificationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $project;
    public $user;
    public function __construct(Project $project, User $user)
    {
        $this->project = $project;
        $this->user = $user;
    }
    public function build()
    {
        return $this->subject('New Project Added')
            ->view('emails.project_notification');
    }
}
