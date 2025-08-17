<?php

namespace App\Http\Controllers\Forms;

use App\Exports\FormSubmissionExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerFormRequest;
use App\Http\Requests\FormSubmissionExportRequest;

use App\Http\Resources\FormSubmissionResource;
use App\Http\Resources\ExportJobStatusResource;
use App\Jobs\Form\StoreFormSubmissionJob;
use App\Jobs\Form\ExportFormSubmissionsJob;
use App\Models\Forms\Form;
use App\Models\Forms\FormSubmission;
use App\Service\Forms\FormExportService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Vinkla\Hashids\Facades\Hashids;

class FormSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['submissionFile']]);
        $this->middleware('signed', ['only' => ['submissionFile']]);
    }

    public function submissions(string $id)
    {
        $form = Form::findOrFail((int) $id);
        $this->authorize('view', $form);

        return FormSubmissionResource::collection($form->submissions()->paginate(100));
    }

    public function update(AnswerFormRequest $request, $id, $submissionId)
    {
        $form = $request->form;
        $this->authorize('update', $form);

        $submissionData = $request->validated();
        $submissionData['submission_id'] = $submissionId;
        $job = new StoreFormSubmissionJob($request->form, $submissionData);
        $job->handle();

        $data = new FormSubmissionResource(FormSubmission::findOrFail($submissionId));

        return $this->success([
            'message' => 'Record successfully updated.',
            'data' => $data,
        ]);
    }

    // ===== EXPORT METHODS =====

    public function export(FormSubmissionExportRequest $request, string $id, FormExportService $exportService)
    {
        $form = $request->form;
        $this->authorize('view', $form);

        $displayColumns = collect($request->columns)->filter(fn($value, $key) => $value === true)->toArray();

        // Check if we should process asynchronously
        if ($exportService->shouldExportAsync($form)) {
            return $this->startAsyncExport($form, $displayColumns, $exportService);
        }

        // Process synchronously for small exports
        return $this->processSyncExport($form, $displayColumns, $exportService);
    }

    public function exportStatus(string $id, string $jobId, FormExportService $exportService)
    {
        $form = Form::findOrFail((int) $id);
        $this->authorize('view', $form);

        $cacheKey = $exportService->getCacheKey($jobId);
        $jobData = Cache::get($cacheKey);

        if (!$jobData) {
            return $this->error([
                'message' => 'Export job not found or has expired.'
            ], 404);
        }

        // Add job_id to the response data
        $jobData['job_id'] = $jobId;

        return new ExportJobStatusResource($jobData);
    }

    private function startAsyncExport(Form $form, array $displayColumns, FormExportService $exportService)
    {
        $jobId = $exportService->generateJobId();

        ExportFormSubmissionsJob::dispatch($form, $displayColumns, $jobId, auth()->id());

        return $this->success([
            'message' => 'Export started. Large export will be processed in the background.',
            'job_id' => $jobId,
            'is_async' => true
        ]);
    }

    private function processSyncExport(Form $form, array $displayColumns, FormExportService $exportService)
    {
        $allRows = [];
        foreach ($form->submissions as $submission) {
            $allRows[] = $exportService->formatSubmissionForExport($form, $submission, $displayColumns);
        }

        $csvExport = (new FormSubmissionExport($allRows));

        return Excel::download(
            $csvExport,
            $form->slug . '-submission-data.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function submissionFile($id, $fileName)
    {
        $fileName = Str::of(PublicFormController::FILE_UPLOAD_PATH)->replace('?', $id) . '/'
            . urldecode($fileName);

        if (! Storage::exists($fileName)) {
            return $this->error([
                'message' => 'File not found.',
            ], 404);
        }

        if (config('filesystems.default') !== 's3') {
            return response()->file(Storage::path($fileName));
        }

        return redirect(
            Storage::temporaryUrl($fileName, now()->addMinute())
        );
    }



    public function destroy($id, $submissionId)
    {
        $form = Form::findOrFail((int) $id);
        $this->authorize('delete', $form);

        $submission = $form->submissions()->where('id', $submissionId)->firstOrFail();
        $submission->delete();

        return $this->success([
            'message' => 'Record successfully removed.',
        ]);
    }
}
