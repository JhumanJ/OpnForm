<?php

namespace App\Policies;

use App\Integrations\OAuth\OAuthProviderService;
use App\Models\Integration\FormIntegration;
use App\Models\OAuthProvider;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OAuthProviderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, OAuthProvider $provider)
    {
        return $provider->user()->is($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, OAuthProvider $provider)
    {
        return $provider->user()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, OAuthProvider $provider)
    {
        $integrations = FormIntegration::where('oauth_id', $provider->id)->get();
        if ($integrations->count() > 0) {
            return $this->denyWithStatus(400, 'This connection cannot be removed because there is already an integration using it.');
        }

        if ($provider->provider->value === OAuthProviderService::Stripe->value) {
            $formsUsingStripe = $user->forms()
                ->get()
                ->filter(function ($form) use ($provider) {
                    return collect($form->properties)
                        ->some(fn ($prop) => ($prop['stripe_account_id'] ?? null) === $provider->id);
                })
                ->isNotEmpty();
            if ($formsUsingStripe) {
                return $this->denyWithStatus(400, 'This Stripe connection cannot be removed because it is being used in a form payment field.');
            }
        }

        return $provider->user()->is($user);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, OAuthProvider $provider)
    {
        return $provider->user()->is($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, OAuthProvider $provider)
    {
        return $provider->user()->is($user);
    }
}
