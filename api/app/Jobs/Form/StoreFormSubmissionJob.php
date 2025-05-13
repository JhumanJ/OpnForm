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

/**
 * Job to store form submissions
 *
 * This job handles the storage of form submissions, including processing of metadata
 * and special field types like files and signatures.
 *
 * The job accepts all data in the submissionData array, including metadata fields:
 * - submission_id: ID of an existing submission to update (must be an integer)
 * - completion_time: Time in seconds it took to complete the form
 * - is_partial: Whether this is a partial submission (will be stored with STATUS_PARTIAL)
 *   If not specified, submissions are treated as complete by default.
 *
 * These metadata fields will be automatically extracted and removed from the stored form data.
 *
 * For partial submissions:
 * - The submission will be stored with STATUS_PARTIAL
 * - All file uploads and signatures will be processed normally
 * - The submission can later be updated to STATUS_COMPLETED when the user completes the form
 */
class StoreFormSubmissionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ?int $submissionId = null;
    private ?array $formData = null;
    private ?int $completionTime = null;
    private bool $isPartial = false;

    /**
     * Create a new job instance.
     *
     * @param Form $form The form being submitted
     * @param array $submissionData Form data including metadata fields (submission_id, completion_time, etc.)
     * @return void
     */
    public function __construct(public Form $form, public array $submissionData)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->extractMetadata();
        $this->formData = $this->getFormData();
        $this->addHiddenPrefills($this->formData);
        $this->storeSubmission($this->formData);
        $this->formData['submission_id'] = $this->submissionId;
        if (!$this->isPartial) {
            FormSubmitted::dispatch($this->form, $this->formData);
        }
    }

    /**
     * Extract metadata from submission data
     *
     * This method extracts and removes metadata fields from the submission data:
     * - submission_id
     * - completion_time
     * - is_partial
     */
    private function extractMetadata(): void
    {
        if (isset($this->submissionData['completion_time'])) {
            $this->completionTime = $this->submissionData['completion_time'];
            unset($this->submissionData['completion_time']);
        }
        if (isset($this->submissionData['submission_id']) && $this->submissionData['submission_id']) {
            if (is_numeric($this->submissionData['submission_id'])) {
                $this->submissionId = (int)$this->submissionData['submission_id'];
            }
            unset($this->submissionData['submission_id']);
        }
        if (isset($this->submissionData['is_partial'])) {
            $this->isPartial = (bool)$this->submissionData['is_partial'];
            unset($this->submissionData['is_partial']);
        }
    }

    /**
     * Get the submission ID
     *
     * @return int|null
     */
    public function getSubmissionId()
    {
        return $this->submissionId;
    }

    /**
     * Store the submission in the database
     *
     * @param array $formData
     */
    private function storeSubmission(array $formData)
    {
        $submission = $this->submissionId
            ? $this->form->submissions()->findOrFail($this->submissionId)
            : new FormSubmission();
        if (!$this->submissionId) {
            $submission->form_id = $this->form->id;
        }
        $submission->data = $formData;
        $submission->completion_time = $this->completionTime;
        $submission->status = $this->isPartial
            ? FormSubmission::STATUS_PARTIAL
            : FormSubmission::STATUS_COMPLETED;
        $submission->save();
        $this->submissionId = $submission->id;
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
                    $processedFiles = [];
                    foreach ($answerValue as $fileItem) {
                        if (is_string($fileItem) && !empty($fileItem)) {
                            $singleStoredFile = $this->storeFile($fileItem);
                            if ($singleStoredFile) {
                                $processedFiles[] = $singleStoredFile;
                            }
                        }
                    }
                    $finalData[$field['id']] = $processedFiles;
                } else {
                    if (is_string($answerValue) && !empty($answerValue)) {
                        $singleFileResult = $this->storeFile($answerValue);
                        $finalData[$field['id']] = $singleFileResult;
                    } else {
                        $finalData[$field['id']] = $this->storeFile($answerValue); // Handles null/empty $answerValue by returning null
                    }
                }
            } else {
                // Standard field processing (text, ID generation, etc.)
                if ($field['type'] == 'text' && isset($field['generates_uuid']) && $field['generates_uuid']) {
                    $finalData[$field['id']] = ($this->form->is_pro) ? Str::uuid()->toString() : 'Please upgrade your OpenForm subscription to use our ID generation features';
                } elseif ($field['type'] == 'text' && isset($field['generates_auto_increment_id']) && $field['generates_auto_increment_id']) {
                    $finalData[$field['id']] = ($this->form->is_pro) ? (string) ($this->form->submissions_count + 1) : 'Please upgrade your OpenForm subscription to use our ID generation features';
                } else {
                    $finalData[$field['id']] = $answerValue;
                }
            }
            // Special field types
            if ($field['type'] == 'signature') {
                $finalData[$field['id']] = $this->storeSignature($answerValue);
            }
            if ($field['type'] == 'phone_number' && $answerValue && ctype_alpha(substr($answerValue, 0, 2)) && (!isset($field['use_simple_text_input']) || !$field['use_simple_text_input'])) {
                $finalData[$field['id']] = substr($answerValue, 2);
            }
        }
        return $finalData;
    }

    // This is use when updating a record, and file uploads aren't changed.
    private function isSkipForUpload($value)
    {
        $parser = StorageFileNameParser::parse($value);
        $canonicalStoredName = $parser->getMovedFileName();

        if (!$canonicalStoredName) {
            return false; // Input $value couldn't be resolved to a canonical stored name format
        }

        $destinationPath = Str::of(PublicFormController::FILE_UPLOAD_PATH)->replace('?', $this->form->id);
        $fullPathToCheck = $destinationPath . '/' . $canonicalStoredName;
        return Storage::exists($fullPathToCheck);
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
        // Handle pre-existing full URLs (e.g., from prefill)
        if (filter_var($value, FILTER_VALIDATE_URL) !== false && str_contains($value, parse_url(config('app.url'))['host'])) {
            $fileName = explode('?', basename($value))[0];
            $path = FormController::ASSETS_UPLOAD_PATH . '/' . $fileName; // Assuming assets are in a defined path
            $newPath = Str::of(PublicFormController::FILE_UPLOAD_PATH)->replace('?', $this->form->id);
            Storage::move($path, $newPath . '/' . $fileName);
            return $fileName;
        }

        $shouldSkip = $this->isSkipForUpload($value);

        if ($shouldSkip) {
            // File (based on canonical name derived from $value) already exists in permanent storage.
            // Return its canonical name.
            $parser = StorageFileNameParser::parse($value);
            return $parser->getMovedFileName() ?? $value; // Fallback to $value if canonical somehow fails (defensive)
        }

        // Process as a new file upload (or one whose temp version needs to be moved)
        $fileNameParser = StorageFileNameParser::parse($value); // $value is the temp file reference (e.g., originalname_uuid.ext or uuid)

        if (!$fileNameParser || !$fileNameParser->uuid) {
            return null; // Cannot derive UUID from the reference
        }
        $fileNameInTmp = PublicFormController::TMP_FILE_UPLOAD_PATH . $fileNameParser->uuid;
        if (!Storage::exists($fileNameInTmp)) {
            return null; // Temporary file not found
        }
        $movedFileName = $fileNameParser->getMovedFileName(); // This is the canonical name for storage
        if (empty($movedFileName)) {
            return null; // Canonical name generation failed
        }
        $newPath = Str::of(PublicFormController::FILE_UPLOAD_PATH)->replace('?', $this->form->id);
        $completeNewFilename = $newPath . '/' . $movedFileName;
        Storage::move($fileNameInTmp, $completeNewFilename);
        return $movedFileName;
    }

    private function storeSignature(?string $value)
    {
        // If $value looks like a filename (already processed, e.g. during skip or previous handling)
        if ($value && preg_match('/^[\/\w\-. ]+$/', $value)) {
            return $this->storeFile($value); // Re-run through storeFile for consistency / skip logic
        }
        // If $value is base64 data
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
     * Get the processed form data including the submission ID
     *
     * @return array
     */
    public function getProcessedData(): array
    {
        if ($this->formData === null) {
            $this->formData = $this->getFormData();
        }
        $data = $this->formData;
        $data['submission_id'] = $this->submissionId;
        return $data;
    }
}
