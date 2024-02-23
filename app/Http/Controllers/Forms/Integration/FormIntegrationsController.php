<?php

namespace App\Http\Controllers\Forms\Integration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Integration\FormIntegrationsRequest;
use App\Models\Forms\Form;
use App\Models\Integration\FormIntegrations;
use Illuminate\Http\Request;
use Spatie\WebhookServer\WebhookCall;

class FormIntegrationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(FormIntegrationsRequest $request, string $id)
    {
        $form = Form::findOrFail((int) $id);
        $this->authorize('update', $form);

        $integration = FormIntegrations::create([
            'form_id' => $form->id,
            'status' => FormIntegrations::STATUS_ACTIVE,
            'integration_id' => $request->get('integration_id'),
            'logic' => $request->get('logic') ?? [],
            'data' => $request->get('data') ?? []
        ]);

        return $this->success([
            'message' => 'Integration created.',
            'integration' => $integration
        ]);
    }

    public function update(FormIntegrationsRequest $request, string $id, string $integrationid)
    {
        return $this->success([
            'message' => 'Integration updated.',
        ]);
    }

    public function delete(FormIntegrationsRequest $request, string $id, string $integrationid)
    {
        return $this->success([
            'message' => 'Integration deleted.',
        ]);
    }
}
