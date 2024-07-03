<?php

namespace App\Jobs\Billing;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveWorkspaceGuests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // If pricing not enabled
        if (!config('services.pricing.enabled')) {
            return;
        }

        if ($this->user->is_subscribed) {
            return;
        }

        // User is not subscribed anymore - remove guests
        $this->user->workspaces->each(function ($workspace) {
            if ($workspace->is_pro) {
                // Another user still has pro subscription
                return;
            }

            // Detach all users from the workspace (except the owner)
            $workspace->users()->where('users.id','!=', $this->user->id)->detach();
        });
    }
}
