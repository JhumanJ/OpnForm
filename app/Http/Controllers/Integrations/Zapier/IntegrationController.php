<?php

namespace App\Http\Controllers\Integrations\Zapier;

use App\Http\Requests\Zapier\CreateIntegrationRequest;
use App\Http\Requests\Zapier\DeleteIntegrationRequest;

class IntegrationController
{
    public function store(CreateIntegrationRequest $request)
    {
        $request->form()->integrations()
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
        $request->form()
            ->integrations()
            ->where('data->hook_url', $request->input('hookUrl'))
            ->delete();

        return response()->json();
    }
}
