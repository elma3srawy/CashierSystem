<?php

namespace App\Providers;

use App\Events\InvoiceProcessed;
use App\Listeners\LogOrderPrice;
use App\Events\RegisterOrderPricing;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\CalculateWeeklySales;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        RegisterOrderPricing::class => [
            LogOrderPrice::class,
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Event::listen(
            InvoiceProcessed::class,
            CalculateWeeklySales::class,
        );

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
