<?php
namespace App\Listeners;
use App\Events\ProjectCreated;
use App\Services\WhatsappService;

class SendProjectNotificationWhatsapp
{
    public function handle(ProjectCreated $event)
    {
        app(WhatsappService::class)->sendProjectNotification($event->client->mobile, $event->project);
        app(WhatsappService::class)->sendProjectNotification($event->assignedUser->mobile, $event->project);
    }
}
