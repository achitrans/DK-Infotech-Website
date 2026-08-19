<?php
namespace App\Providers;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\UserCreated;
use App\Listeners\SendWelcomeEmail;
use App\Listeners\SendWelcomeWhatsapp;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserCreated::class => [
            SendWelcomeEmail::class,
            SendWelcomeWhatsapp::class,
        ],
        \App\Events\ProjectCreated::class => [
            \App\Listeners\SendProjectNotificationEmail::class,
            \App\Listeners\SendProjectNotificationWhatsapp::class,
        ],
            \App\Events\ClientKycStatusUpdated::class => [
                \App\Listeners\NotifyClientKycStatus::class,
            ],
            \App\Events\UserKycStatusUpdated::class => [
                \App\Listeners\NotifyUserKycStatus::class,
            ],
    ];
}
