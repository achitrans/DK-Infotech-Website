<?php
namespace App\Listeners;
use App\Events\ProjectCreated;
use App\Jobs\SendEmailJob;
use App\Services\EmailValidator;
use App\Mail\ProjectNotificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendProjectNotificationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ProjectCreated $event)
    {
        if($event->client && $event->client->email) {
            if (EmailValidator::validate($event->client->email)) {
                try {
                    SendEmailJob::dispatch($event->client->email, new ProjectNotificationMail($event->project, $event->client));
                } catch (Throwable $e) {
                    Log::error('Failed dispatching project email to client: ' . $e->getMessage());
                }
            }
        }

        if($event->assignedUser && $event->assignedUser->email) {
            if (EmailValidator::validate($event->assignedUser->email)) {
                try {
                    SendEmailJob::dispatch($event->assignedUser->email, new ProjectNotificationMail($event->project, $event->assignedUser));
                } catch (Throwable $e) {
                    Log::error('Failed dispatching project email to assigned user: ' . $e->getMessage());
                }
            }
        }

    }
}

