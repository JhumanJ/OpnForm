<?php

namespace App\Jobs\Form;

use App\Events\Forms\FormSubmitted;
use App\Http\Controllers\Forms\FormController;
use App\Http\Controllers\Forms\PublicFormController;
use App\Http\Requests\AnswerFormRequest;
use App\Models\Forms\Form;
use App\Models\Forms\FormSubmission;
use App\Service\Forms\FormLogicPropertyResolver;
use App\Service\Storage\StorageFileNameParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class StoreFormSubmissionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ?string $submissionId = null;
    private ?array $formData = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Form $form, public array $submissionData, public ?int $completionTime = null)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->formData = $this->getFormData();
        $this->addHiddenPrefills($this->formData);

        $this->storeSubmission($this->formData);

        $this->formData['submission_id'] = $this->submissionId;
        FormSubmitted::dispatch($this->form, $this->formData);
    }

    public function getSubmissionId()
    {
        return $this->submissionId;
    }

    public function setSubmissionId(int $id)
    {
        $this->submissionId = $id;

        return $this;
    }

    private function storeSubmission(array $formData)
    {
        // Create or update record
        if ($previousSubmission = $this->submissionToUpdate()) {
            $previousSubmission->data = $formData;
            $previousSubmission->completion_time = $this->completionTime;
            $previousSubmission->save();
            $this->submissionId = $previousSubmission->id;
        } else {
            $response = $this->form->submissions()->create([
                'data' => $formData,
                'completion_time' => $this->completionTime,
            ]);
            $this->submissionId = $response->id;
        }
    }

    /**
     * Search for Submission record to update and returns it
     */
    private function submissionToUpdate(): ?FormSubmission
    {
        if ($this->submissionId) {
            return $this->form->submissions()->findOrFail($this->submissionId);
        }
        if ($this->form->editable_submissions && isset($this->submissionData['submission_id']) && $this->submissionData['submission_id']) {
            $submissionId = $this->submissionData['submission_id'] ? Hashids::decode($this->submissionData['submission_id']) : false;
            $submissionId = $submissionId[0] ?? null;

            return $this->form->submissions()->findOrFail($submissionId);
        }

        return null;
    }

    /**
     * Retrieve data from request object, and pre-format it if needed.
     * - Replace notionforms id with notion field ids
     * - Clean \ in select id values
     * - Stores file and replace value with url
     * - Generate auto increment id & unique id features for rich text field
     */
    private function getFormData()
    {
        $data = $this->submissionData;
        $finalData = [];

        $properties = collect($this->form->properties);

        // Do required transformation per type (e.g. file uploads)
        foreach ($data as $answerKey => $answerValue) {
            $field = $properties->where('id', $answerKey)->first();
            if (!$field) {
                continue;
            }

            if (
                ($field['type'] == 'url' && isset($field['file_upload']) && $field['file_upload'])
                || $field['type'] == 'files'
            ) {
                if (is_array($answerValue)) {
                    $finalData[$field['id']] = [];
                    foreach ($answerValue as $file) {
                        $finalData[$field['id']][] = $this->storeFile($file);
                    }
                } else {
                    $finalData[$field['id']] = $this->storeFile($answerValue);
                }
            } else {
                if ($field['type'] == 'text' && isset($field['generates_uuid']) && $field['generates_uuid']) {
                    $finalData[$field['id']] = ($this->form->is_pro) ? Str::uuid() : 'Please upgrade your OpenForm subscription to use our ID generation features';
                } else {
                    if ($field['type'] == 'text' && isset($field['generates_auto_increment_id']) && $field['generates_auto_increment_id']) {
                        $finalData[$field['id']] = ($this->form->is_pro) ? (string) ($this->form->submissions_count + 1) : 'Please upgrade your OpenForm subscription to use our ID generation features';
                    } else {
                        $finalData[$field['id']] = $answerValue;
                    }
                }
            }

            // For Singrature
            if ($field['type'] == 'signature') {
                $finalData[$field['id']] = $this->storeSignature($answerValue);
            }

            // For Phone
            if ($field['type'] == 'phone_number' && $answerValue && ctype_alpha(substr($answerValue, 0, 2)) && (!isset($field['use_simple_text_input']) || !$field['use_simple_text_input'])) {
                $finalData[$field['id']] = substr($answerValue, 2);
            }
        }

        return $finalData;
    }

    // This is use when updating a record, and file uploads aren't changed.
    private function isSkipForUpload($value)
    {
        $newPath = Str::of(PublicFormController::FILE_UPLOAD_PATH)->replace('?', $this->form->id);

        return Storage::exists($newPath . '/' . $value);
    }

    /**
     * Custom Back-end Value formatting. Use case:
     * - File uploads (move file from tmp storage to persistent)
     *
     * File can have 2 formats:
     * - file_name-{uuid}.{ext}
     * - {uuid}
     */
    private function storeFile($value, ?bool $isPublic = null)
    {
        if (is_null($value) || empty($value)) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL) !== false && str_contains($value, parse_url(config('app.url'))['host'])) {  // In case of prefill we have full url so convert to s3
            $fileName = explode('?', basename($value))[0];
            $path = FormController::ASSETS_UPLOAD_PATH . '/' . $fileName;
            $newPath = Str::of(PublicFormController::FILE_UPLOAD_PATH)->replace('?', $this->form->id);
            Storage::move($path, $newPath . '/' . $fileName);

            return $fileName;
        }

        if ($this->isSkipForUpload($value)) {
            return $value;
        }

        $fileNameParser = StorageFileNameParser::parse($value);
        if (!$fileNameParser || !$fileNameParser->uuid) {
            return null;
        }

        // Make sure we retrieve the file in tmp storage, move it to persistent
        $fileName = PublicFormController::TMP_FILE_UPLOAD_PATH . '/' . $fileNameParser->uuid;
        if (!Storage::exists($fileName)) {
            // File not found, we skip
            return null;
        }
        $newPath = Str::of(PublicFormController::FILE_UPLOAD_PATH)->replace('?', $this->form->id);
        $completeNewFilename = $newPath . '/' . $fileNameParser->getMovedFileName();

        \Log::debug('Moving file to permanent storage.', [
            'uuid' => $fileNameParser->uuid,
            'destination' => $completeNewFilename,
            'form_id' => $this->form->id,
            'form_slug' => $this->form->slug,
        ]);
        Storage::move($fileName, $completeNewFilename);

        return $fileNameParser->getMovedFileName();
    }

    private function storeSignature(?string $value)
    {
        if ($value && preg_match('/^[\/\w\-. ]+$/', $value)) {  // If it's filename
            return $this->storeFile($value);
        }

        if ($value == null || !isset(explode(',', $value)[1])) {
            return null;
        }

        $fileName = 'sign_' . (string) Str::uuid() . '.png';
        $newPath = Str::of(PublicFormController::FILE_UPLOAD_PATH)->replace('?', $this->form->id);
        $completeNewFilename = $newPath . '/' . $fileName;

        Storage::put($completeNewFilename, base64_decode(explode(',', $value)[1]));

        return $fileName;
    }

    /**
     * Adds prefill from hidden fields
     *
     * @param  AnswerFormRequest  $request
     */
    private function addHiddenPrefills(array &$formData): void
    {
        // Find hidden properties with prefill, set values
        collect($this->form->properties)->filter(function ($property) {
            return isset($property['hidden'])
                && isset($property['prefill'])
                && FormLogicPropertyResolver::isHidden($property, $this->submissionData)
                && !is_null($property['prefill'])
                && !in_array($property['type'], ['files'])
                && !($property['type'] == 'url' && isset($property['file_upload']) && $property['file_upload']);
        })->each(function (array $property) use (&$formData) {
            if ($property['type'] === 'date' && isset($property['prefill_today']) && $property['prefill_today']) {
                $formData[$property['id']] = now()->format((isset($property['with_time']) && $property['with_time']) ? 'Y-m-d H:i' : 'Y-m-d');
            } else {
                $formData[$property['id']] = $property['prefill'];
            }
        });
    }

    /**
     * Get the processed form data after all transformations
     */
    public function getProcessedData(): array
    {
        if ($this->formData === null) {
            $this->formData = $this->getFormData();
        }
        return $this->formData;
    }
}
