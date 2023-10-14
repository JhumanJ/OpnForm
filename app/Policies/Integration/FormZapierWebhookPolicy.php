<?php

namespace App\Policies\Integration;

use App\Models\Integration\FormZapierWebhook;
use App\Models\User;
use App\Policies\FormPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormZapierWebhookPolicy
{
    use HandlesAuthorization;

    protected FormPolicy $formPolicy;

    public function __construct()
    {
        $this->formPolicy = new FormPolicy();
    }

    public function store(User $user, FormZapierWebhook $webhook)
    {
        return ($webhook?->form) ? $this->formPolicy->update($user, $webhook->form) : false; // && $user->is_subscribed;
    }

    public function delete(User $user, FormZapierWebhook $webhook)
    {
        return ($webhook?->form) ? $this->formPolicy->update($user, $webhook->form) : false; // && $user->is_subscribed;
    }
}
