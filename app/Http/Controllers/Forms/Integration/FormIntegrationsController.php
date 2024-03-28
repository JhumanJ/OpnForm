<?php

namespace App\Http\Controllers\Forms\Integration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Integration\FormIntegrationsRequest;
use App\Models\Forms\Form;
use App\Models\Integration\FormIntegration;

class FormIntegrationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(string $id)
    {
        $form = Form::findOrFail((int)$id);
        $this->authorize('view', $form);

        return FormIntegration::where('form_id', $form->id)->get();
    }

    public function create(FormIntegrationsRequest $request, string $id)
    {
        $form = Form::findOrFail((int)$id);
        $this->authorize('update', $form);

        $formIntegration = FormIntegration::create(
            array_merge([
                'form_id' => $form->id,
            ], $request->toIntegrationData())
        );

        return $this->success([
            'message' => 'Form Integration was created.',
            'form_integration' => $formIntegration
        ]);
    }

    public function update(FormIntegrationsRequest $request, string $id, string $integrationid)
    {
        $form = Form::findOrFail((int)$id);
        $this->authorize('update', $form);

        $formIntegration = FormIntegration::findOrFail((int)$integrationid);
        $formIntegration->update($request->toIntegrationData());

        return $this->success([
            'message' => 'Form Integration was updated.',
            'form_integration' => $formIntegration
        ]);
    }

    public function destroy(string $id, string $integrationid)
    {
        $form = Form::findOrFail((int)$id);
        $this->authorize('update', $form);

        $formIntegration = FormIntegration::findOrFail((int)$integrationid);
        $formIntegration->delete();

        return $this->success([
            'message' => 'Form Integration was deleted.'
        ]);
    }
}
