<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Jhumanj\LaravelModelStats\LaravelModelStats;
use Jhumanj\LaravelModelStats\ModelStatsServiceProvider as Provider;

class ModelStatsServiceProvider extends Provider
{
    /**
     * Register the LaravelModelStats gate.
     *
     * This gate determines who can access ModelStats in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewModelStats', function ($user) {
            return in_array($user->email, [
                'julien@notionforms.io',
            ]);
        });
    }
}
