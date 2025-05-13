<?php

use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\OAuthProvider;

beforeEach(function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);

    // Create OAuth provider for Stripe
    $this->stripeAccount = OAuthProvider::factory()->for($user)->create([
        'provider' => 'stripe',
        'provider_user_id' => 'acct_1LhEwZCragdZygxE'
    ]);

    // Create form with payment block
    $this->form = $this->createForm($user, $workspace);
    $this->form->properties = array_merge($this->form->properties, [
        [
            'type' => 'payment',
            'stripe_account_id' => $this->stripeAccount->id,
            'amount' => 99.99,
            'currency' => 'USD'
        ]
    ]);
    $this->form->update();
});

it('can get stripe account for form', function () {
    $this->getJson(route('forms.stripe-connect.get-account', $this->form->slug))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) {
            return $json->has('stripeAccount')
                ->where('stripeAccount', fn ($id) => str_starts_with($id, 'acct_'))
                ->etc();
        });
});

it('cannot create payment intent for non-public form', function () {
    // Update form visibility to private
    $this->form->update(['visibility' => 'private']);

    $this->postJson(route('forms.stripe-connect.create-intent', $this->form->slug))
        ->assertStatus(404)
        ->assertJson([
            'message' => 'Form not found.'
        ]);
});

it('cannot create payment intent for form without payment block', function () {
    // Remove payment block entirely
    $properties = collect($this->form->properties)
        ->reject(fn ($block) => $block['type'] === 'payment')
        ->values()
        ->all();

    $this->form->update(['properties' => $properties]);

    $this->postJson(route('forms.stripe-connect.create-intent', $this->form->slug))
        ->assertStatus(400)
        ->assertJson([
            'type' => 'error',
            'message' => 'Form does not have a payment block. If you just added a payment block, please save the form and try again.'
        ]);
});

it('cannot create payment intent with invalid stripe account', function () {
    // Update payment block with non-existent stripe account
    $properties = collect($this->form->properties)->map(function ($block) {
        if ($block['type'] === 'payment') {
            $block['stripe_account_id'] = 999999;
        }
        return $block;
    })->all();

    $this->form->update(['properties' => $properties]);

    $this->postJson(route('forms.stripe-connect.create-intent', $this->form->slug))
        ->assertStatus(400)
        ->assertJson([
            'message' => 'Failed to find Stripe account'
        ]);
});
