<?php
namespace App\Listeners;
use App\Events\UserCreated;
use App\Services\WhatsappService;

class SendWelcomeWhatsapp
{
    public function handle(UserCreated $event)
    {
        app(WhatsappService::class)->sendWelcome($event->user);
    }
}
