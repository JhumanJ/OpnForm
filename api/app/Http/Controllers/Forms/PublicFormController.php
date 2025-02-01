<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerFormRequest;
use App\Http\Resources\FormResource;
use App\Http\Resources\FormSubmissionResource;
use App\Jobs\Form\StoreFormSubmissionJob;
use App\Models\Forms\Form;
use App\Models\Forms\FormSubmission;
use App\Service\Forms\FormSubmissionProcessor;
use App\Service\Forms\FormCleaner;
use App\Service\WorkspaceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

class PublicFormController extends Controller
{
    public const FILE_UPLOAD_PATH = 'forms/?/submissions';

    public const TMP_FILE_UPLOAD_PATH = 'tmp/';

    public function show(Request $request, string $slug)
    {
        $form = Form::whereSlug($slug)->whereIn('visibility', ['public', 'closed'])->firstOrFail();
        if ($form->workspace == null) {
            // Workspace deleted
            return $this->error([
                'message' => 'Form not found.',
            ], 404);
        }

        $formCleaner = new FormCleaner();

        // Disable pro features if needed
        $form->fill(
            $formCleaner
                ->processForm($request, $form)
                ->performCleaning($form->workspace)
                ->getData()
        );

        // Increase form view counter if not login
        if (!Auth::check()) {
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
        $path = FormController::ASSETS_UPLOAD_PATH . '/' . $assetFileName;
        if (!Storage::exists($path)) {
            return $this->error([
                'message' => 'File not found.',
                'file_name' => $assetFileName,
            ]);
        }

        $internal_url = Storage::temporaryUrl($path, now()->addMinutes(5));

        foreach (config('filesystems.disks.s3.temporary_url_rewrites') as $from => $to) {
            $internal_url = str_replace($from, $to, $internal_url);
        }

        return redirect()->to($internal_url);
    }

    public function answer(AnswerFormRequest $request, FormSubmissionProcessor $formSubmissionProcessor)
    {
        $form = $request->form;
        $isFirstSubmission = ($form->submissions_count === 0);
        $submissionId = false;

        $submissionData = $request->validated();
        $completionTime = $request->get('completion_time') ?? null;
        unset($submissionData['completion_time']); // Remove completion_time from the main data array

        $job = new StoreFormSubmissionJob($form, $submissionData, $completionTime);

        if ($formSubmissionProcessor->shouldProcessSynchronously($form)) {
            $job->handle();
            $submissionId = Hashids::encode($job->getSubmissionId());
            // Update submission data with generated values for redirect URL
            $submissionData = $job->getProcessedData();
        } else {
            StoreFormSubmissionJob::dispatch($form, $submissionData, $completionTime);
        }

        return $this->success(array_merge([
            'message' => 'Form submission saved.',
            'submission_id' => $submissionId,
            'is_first_submission' => $isFirstSubmission,
        ], $formSubmissionProcessor->getRedirectData($form, $submissionData)));
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

        $submission = FormSubmission::find($submissionId);
        if (!$submission) {
            return $this->error([
                'message' => 'Submission not found.',
            ]);
        }

        $submission = new FormSubmissionResource($submission);
        $submission->publiclyAccessed();

        if ($submission->form_id != $form->id) {
            return $this->error([
                'message' => 'Not allowed.',
            ], 403);
        }

        return $this->success($submission->toArray($request));
    }
}
