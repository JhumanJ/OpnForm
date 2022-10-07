<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerFormRequest;
use App\Http\Resources\FormResource;
use App\Jobs\Form\StoreFormSubmissionJob;
use App\Models\Forms\Form;
use App\Service\Forms\FormCleaner;
use App\Service\WorkspaceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PublicFormController extends Controller
{

    const FILE_UPLOAD_PATH = 'forms/?/submissions';
    const TMP_FILE_UPLOAD_PATH = 'tmp/';

    public function show(Request $request, string $slug)
    {
        $form = Form::whereSlug($slug)->whereVisibility('public')->firstOrFail();
        if ($form->workspace == null) {
            // Workspace deleted
            return $this->error([
                'message' => 'Form not found.'
            ], 404);
        }

        $formCleaner = new FormCleaner();

        // Disable pro features if needed
        $form->fill($formCleaner
            ->processForm($request, $form)
            ->performCleaning($form->workspace)
            ->getData()
        );

        // Increase form view counter if not login
        if(!Auth::check()){
            $form->views()->create();
        }

        $formResource = new FormResource($form);
        $formResource->setCleanings($formCleaner->getPerformedCleanings());
        return $formResource;
    }

    public function listUsers(Request $request)
    {
        // Check that form has user field
        $form = $request->form;
        if (!$form->has_user_field) {
            return [];
        }

        // Use serializer
        $workspace = $form->workspace;
        return (new WorkspaceHelper($workspace))->getAllUsers();
    }

    public function showAsset($assetFileName)
    {
        $path = FormController::ASSETS_UPLOAD_PATH.'/'.$assetFileName;
        if (!Storage::disk('s3')->exists($path)) {
            return $this->error([
                'message' => 'File not found.',
                'file_name' => $assetFileName
            ]);
        }

        return redirect()->to(Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(5)));
    }

    public function answer(AnswerFormRequest $request)
    {
        $form = $request->form;

        StoreFormSubmissionJob::dispatch($form, $request->validated());
        return $this->success(array_merge([
            'message' => 'Form submission saved.',
        ], $request->form->is_pro && $request->form->redirect_url ? [
            'redirect' => true,
            'redirect_url' => $request->form->redirect_url
        ] : [
            'redirect' => false
        ]));
    }
}
