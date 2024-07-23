<?php

namespace App\Listeners\Billing;

use App\Events\Billing\SubscriptionUpdated;
use App\Jobs\Billing\RemoveWorkspaceGuests;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveWorkspaceGuestsIfNeeded implements ShouldQueue
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
    public function handle(SubscriptionUpdated $event): void
    {
        /**
         * Subscription $subscription
         */
        $subscription = $event->subscription;
        if (!$subscription->valid()) {
            RemoveWorkspaceGuests::dispatch($event->subscription->user);
        }
    }
}
