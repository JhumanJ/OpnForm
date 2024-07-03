<?php

namespace App\Listeners\Billing;

use App\Events\Billing\SubscriptionCreated;

class HandleSubscriptionCreated
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */

    public function handle(SubscriptionCreated $event)
    {
        $user =  $event->subscription->user;

        // Remove branding
        $user->workspaces()->with('forms')->get()->each(function ($workspace) {
            $workspace->forms()->update(['no_branding' => true]);
        });

    }
}
