<?php

namespace App\Http\Controllers\Forms;

use App\Exports\FormSubmissionExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerFormRequest;
use App\Http\Requests\FormSubmissionExportRequest;
use App\Http\Resources\FormSubmissionResource;
use App\Jobs\Form\StoreFormSubmissionJob;
use App\Models\Forms\Form;
use App\Models\Forms\FormSubmission;
use App\Service\Forms\FormSubmissionFormatter;
use Illuminate\Support\Facades\Storage;
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

    public function export(FormSubmissionExportRequest $request, string $id)
    {
        $form = $request->form;
        $this->authorize('view', $form);

        $allRows = [];
        $displayColumns = collect($request->columns)->filter(fn ($value, $key) => $value === true)->toArray();
        foreach ($form->submissions->toArray() as $row) {
            $formatter = (new FormSubmissionFormatter($form, $row['data']))
                ->outputStringsOnly()
                ->setEmptyForNoValue()
                ->showRemovedFields()
                ->showHiddenFields()
                ->useSignedUrlForFiles();
            $formattedData = $formatter->getCleanKeyValue();
            $filteredData = ['id' => Hashids::encode($row['id'])];
            foreach ($displayColumns as $column => $value) {
                $key = collect($formattedData)->keys()->first(fn ($key) => str_contains($key, $column));
                if ($key) {
                    $filteredData[$key] = $formattedData[$key];
                }
            }
            if (isset($displayColumns['created_at'])) {
                $filteredData['created_at'] = date('Y-m-d H:i', strtotime($row['created_at']));
            }
            $allRows[] = $filteredData;
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
}
