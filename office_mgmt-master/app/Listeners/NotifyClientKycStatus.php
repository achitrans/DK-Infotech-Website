<?php
namespace App\Listeners;
use App\Events\ClientKycStatusUpdated;
use App\Jobs\SendEmailJob;
use App\Services\EmailValidator;
use App\Services\WhatsappService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotifyClientKycStatus implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ClientKycStatusUpdated $event)
    {
        if (EmailValidator::validate($event->kyc->user->email)) {
            try {
                SendEmailJob::dispatch($event->kyc->user->email, new \App\Mail\KycStatusMail('client', $event->kyc));
            } catch (Throwable $e) {
                Log::error('Failed dispatching KycStatusMail to client: ' . $e->getMessage());
            }
        }
        // WhatsApp notification
        app()->make(WhatsappService::class)->sendKycStatus('client', $event->kyc->user->mobile, $event->kyc);
    }
}

