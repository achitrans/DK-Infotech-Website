<?php
namespace App\Listeners;
use App\Mail\WelcomeMail;
use App\Events\UserCreated;
use App\Jobs\SendEmailJob;
use App\Services\EmailValidator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserCreated $event)
    {
        try {
            if (EmailValidator::validate($event->user->email)) {
                SendEmailJob::dispatch($event->user->email, new WelcomeMail($event->user), 'email');
            }
        } catch (Throwable $e) {
            Log::channel('email')->error('Failed to dispatch welcome email job: ' . $e->getMessage());
        }

    }
}

