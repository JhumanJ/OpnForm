<?php

namespace Tests;

use App\Models\Forms\Form;
use App\Models\User;
use App\Models\Workspace;
use Database\Factories\FormFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait TestHelpers
{
    /**
     * Creates a workspace for a user
     */
    public function createUserWorkspace(User $user)
    {
        if (!$user) {
            return null;
        }

        $workspace = Workspace::create([
            'name' => 'Form Testing',
            'icon' => '🧪',
        ]);

        $user->workspaces()->sync([
            $workspace->id => [
                'role' => 'admin',
            ],
        ], false);

        return $workspace;
    }

    /**
     * Generates a Form instance (not saved)
     *
     * @return array
     */
    public function makeForm(User $user, Workspace $workspace, array $data = [])
    {
        $dbProperties = [
            [
                'name' => 'Name',
                'type' => 'text',
                'hidden' => false,
                'required' => false,
            ],
            [
                'name' => 'Message',
                'type' => 'text',
                'hidden' => false,
                'required' => false,
                'multi_lines' => true,
            ],
            [
                'name' => 'Number',
                'type' => 'number',
                'hidden' => false,
                'required' => false,
            ],
            [
                'name' => 'Rating',
                'type' => 'rating',
                'hidden' => false,
                'required' => false,
                'rating_max_value' => 5
            ],
            [
                'name' => 'Scale',
                'type' => 'scale',
                'hidden' => false,
                'required' => false,
                'scale_min_value' => 1,
                'scale_max_value' => 10,
                'scale_step_value' => 1,
            ],
            [
                'name' => 'Slider',
                'type' => 'slider',
                'hidden' => false,
                'required' => false,
                'slider_min_value' => 1,
                'slider_max_value' => 100,
                'slider_step_value' => 1,
            ],
            [
                'name' => 'Select',
                'type' => 'select',
                'hidden' => false,
                'required' => false,
                'allow_creation' => false,
                'select' => [
                    'options' => [['id' => 'First', 'name' => 'First'], ['id' => 'Second', 'name' => 'Second']],
                ],
            ],
            [
                'name' => 'Multi Select',
                'type' => 'multi_select',
                'hidden' => false,
                'required' => false,
                'allow_creation' => false,
                'multi_select' => [
                    'options' => [['id' => 'One', 'name' => 'One'], ['id' => 'Two', 'name' => 'Two'], ['id' => 'Three', 'name' => 'Three']],
                ],
            ],
            [
                'name' => 'Date',
                'type' => 'date',
                'hidden' => false,
                'required' => false,
            ],
            [
                'name' => 'Checkbox',
                'type' => 'checkbox',
                'hidden' => false,
                'required' => false,
            ],
            [
                'name' => 'URL',
                'type' => 'url',
                'hidden' => false,
                'required' => false,
            ],
            [
                'name' => 'Email',
                'type' => 'email',
                'hidden' => false,
                'required' => false,
            ],
            [
                'name' => 'Phone Number',
                'type' => 'phone_number',
                'hidden' => false,
                'required' => false,
            ],
            [
                'name' => 'Files',
                'type' => 'files',
                'hidden' => false,
                'required' => false,
            ],
        ];

        return Form::factory()
            ->withProperties(FormFactory::formatProperties($dbProperties))
            ->forWorkspace($workspace)
            ->createdBy($user)
            ->make($data);
    }

    public function createForm(User $user, Workspace $workspace, array $data = [])
    {
        $form = $this->makeForm($user, $workspace, $data);
        $form->save();

        return $form;
    }

    public function createUser(array $data = [])
    {
        return \App\Models\User::factory()->create($data);
    }

    public function createProUser()
    {
        $user = $this->createUser();

        $user->subscriptions()->create([
            'name' => 'default',
            'stripe_id' => Str::random(),
            'stripe_status' => 'active',
            'stripe_price' => Str::random(),
            'quantity' => 1,
        ]);

        return $user;
    }

    public function actingAsUser(?User $user = null)
    {
        if ($user == null) {
            $user = $this->createUser();
        }

        $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSuccessful();

        return $user;
    }

    /**
     * Creates a user with a Pro subscription
     *
     * @return User|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function actingAsProUser(?User $user = null)
    {
        if ($user == null) {
            $user = $this->createProUser();
        }

        return $this->actingAsUser($user);
    }

    public function actingAsGuest()
    {
        if (Auth::check()) {
            Auth::logout();
        }
        $this->assertGuest();
    }

    public function createFormIntegration($integrationId, $formId, $settings = [])
    {
        $data = [
            'status' => true,
            'integration_id' => $integrationId,
            'logic' => null,
            'settings' => $settings
        ];

        $response = $this->postJson(route('open.forms.integration.create', $formId), $data)
            ->assertSuccessful()
            ->assertJson([
                'type' => 'success',
                'message' => 'Form Integration was created.'
            ]);

        return (object) $response->json('form_integration.data');
    }
}
