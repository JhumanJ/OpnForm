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

    public function index(string $id)
    {
        $form = Form::findOrFail((int) $id);
        $this->authorize('view', $form);

        return FormIntegrations::where('form_id', $form->id)->get();
    }

    public function create(FormIntegrationsRequest $request, string $id)
    {
        $form = Form::findOrFail((int) $id);
        $this->authorize('update', $form);

        $formIntegration = FormIntegrations::create([
            'form_id' => $form->id,
            'status' => FormIntegrations::STATUS_ACTIVE,
            'integration_id' => $request->get('integration_id'),
            'data' => $request->get('settings') ?? [],
            'logic' => $request->get('logic') ?? []
        ]);

        return $this->success([
            'message' => 'Form Integration was created.',
            'form_integration' => $formIntegration
        ]);
    }

    public function update(FormIntegrationsRequest $request, string $id, string $integrationid)
    {
        $form = Form::findOrFail((int) $id);
        $this->authorize('update', $form);

        $formIntegration = FormIntegrations::findOrFail((int) $integrationid);
        $formIntegration->update([
            'data' => $request->get('settings') ?? [],
            'logic' => $request->get('logic') ?? []
        ]);

        return $this->success([
            'message' => 'Form Integration was updated.',
            'form_integration' => $formIntegration
        ]);
    }

    public function destroy(string $id, string $integrationid)
    {
        $form = Form::findOrFail((int) $id);
        $this->authorize('update', $form);

        $formIntegrations = FormIntegrations::findOrFail((int) $integrationid);
        $formIntegrations->delete();

        return $this->success([
            'message' => 'Form Integration was deleted.'
        ]);
    }
}
