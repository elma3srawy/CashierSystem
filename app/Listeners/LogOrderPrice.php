<?php

namespace App\Listeners;

use App\Events\RegisterOrderPricing;
use App\Models\OrderPricing;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogOrderPrice
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RegisterOrderPricing $event): void
    {
        OrderPricing::create(['order_id' => $event->order_id , 'price' => $event->payment]);
    }
}
