<?php

namespace App\Http\Controllers\Integrations\Zapier;

use App\Http\Requests\Zapier\CreateIntegrationRequest;
use App\Http\Requests\Zapier\DeleteIntegrationRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class IntegrationController
{
    use AuthorizesRequests;

    public function store(CreateIntegrationRequest $request)
    {
        $form = $request->form();

        $this->authorize('view', $form);

        $form->integrations()
            ->create([
                'integration_id' => 'zapier',
                'status' => 'active',
                'data' => [
                    'hook_url' => $request->input('hookUrl'),
                ],
            ]);

        return response()->json();
    }

    public function destroy(DeleteIntegrationRequest $request)
    {
        $form = $request->form();

        $this->authorize('view', $form);

        $form
            ->integrations()
            ->where('data->hook_url', $request->input('hookUrl'))
            ->delete();

        return response()->json();
    }
}
