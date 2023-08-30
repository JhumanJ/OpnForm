<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerFormRequest;
use App\Http\Resources\FormResource;
use App\Jobs\Form\StoreFormSubmissionJob;
use App\Models\Forms\Form;
use App\Models\Forms\FormSubmission;
use App\Service\Forms\FormCleaner;
use App\Service\WorkspaceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class PublicFormController extends Controller
{

    const FILE_UPLOAD_PATH = 'forms/?/submissions';
    const TMP_FILE_UPLOAD_PATH = 'tmp/';

    public function show(Request $request, string $slug)
    {
        $form = Form::whereSlug($slug)->whereIn('visibility', ['public', 'closed'])->firstOrFail();
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

        return (new FormResource($form))
            ->setCleanings($formCleaner->getPerformedCleanings());
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
        if (!Storage::exists($path)) {
            return $this->error([
                'message' => 'File not found.',
                'file_name' => $assetFileName
            ]);
        }
        
        return redirect()->to(Storage::temporaryUrl($path, now()->addMinutes(5)));
    }

    public function answer(AnswerFormRequest $request)
    {
        $form = $request->form;
        $submissionId = false;

        if ($form->editable_submissions) {
            $job = new StoreFormSubmissionJob($form, $request->validated());
            $job->handle();
            $submissionId = Hashids::encode($job->getSubmissionId());
        }else{
            StoreFormSubmissionJob::dispatch($form, $request->validated());
        }

        return $this->success(array_merge([
            'message' => 'Form submission saved.',
            'submission_id' => $submissionId
        ], $request->form->is_pro && $request->form->redirect_url ? [
            'redirect' => true,
            'redirect_url' => $request->form->redirect_url
        ] : [
            'redirect' => false
        ]));
    }

    public function fetchSubmission(Request $request, string $slug, string $submissionId)
    {
        $submissionId = ($submissionId) ? Hashids::decode($submissionId) : false;
        $submissionId = isset($submissionId[0]) ? $submissionId[0] : false;
        $form = Form::whereSlug($slug)->whereVisibility('public')->firstOrFail();
        if ($form->workspace == null || !$form->editable_submissions || !$submissionId) {
            return $this->error([
                'message' => 'Not allowed.',
            ]);
        }

        $submission = FormSubmission::findOrFail($submissionId);

        if ($submission->form_id != $form->id) {
            return $this->error([
                'message' => 'Not allowed.',
            ], 403);
        }

        return $this->success(['data' => ($submission) ? $submission->data : []]);
    }

}
