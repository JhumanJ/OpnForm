<?php

namespace App\Providers;

use App\Models\Forms\Form;
use App\Models\Integration\FormZapierWebhook;
use App\Models\OAuthProvider;
use App\Models\Template;
use App\Models\Workspace;
use App\Policies\FormPolicy;
use App\Policies\Integration\FormZapierWebhookPolicy;
use App\Policies\OAuthProviderPolicy;
use App\Policies\TemplatePolicy;
use App\Policies\WorkspacePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Form::class => FormPolicy::class,
        Workspace::class => WorkspacePolicy::class,
        FormZapierWebhook::class => FormZapierWebhookPolicy::class,
        Template::class => TemplatePolicy::class,
        OAuthProvider::class => OAuthProviderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        \Illuminate\Support\Facades\Gate::define('viewMailcoach', function ($user = null) {
            return optional($user)->admin;
        });
    }
}
