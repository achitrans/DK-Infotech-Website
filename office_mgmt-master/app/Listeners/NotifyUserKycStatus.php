<?php
namespace App\Listeners;
use App\Events\UserKycStatusUpdated;
use App\Jobs\SendEmailJob;
use App\Services\EmailValidator;
use App\Services\WhatsappService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotifyUserKycStatus implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserKycStatusUpdated $event)
    {
        if (EmailValidator::validate($event->kyc->user->email)) {
            try {
                SendEmailJob::dispatch($event->kyc->user->email, new \App\Mail\KycStatusMail('employee', $event->kyc));
            } catch (Throwable $e) {
                Log::error('Failed dispatching KycStatusMail to user: ' . $e->getMessage());
            }
        }
        // WhatsApp notification
        app()->make(WhatsappService::class)->sendKycStatus('employee', $event->kyc->user->mobile, $event->kyc);
    }
}

