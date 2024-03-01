<?php

namespace App\Rules;

use App\Http\Controllers\Forms\PublicFormController;
use App\Models\Forms\Form;
use App\Service\Storage\StorageFileNameParser;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageFile implements Rule
{
    public string $error = 'Invalid file.';

    public function __construct(public int $maxSize, public array $fileTypes = [], public ?Form $form = null)
    {
    }

    /**
     * File can have 2 formats:
     * - file-name_{uuid}.{ext}
     * - {uuid}
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        // If full path then no need to validate
        if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
            return true;
        }

        // This is use when updating a record, and file uploads aren't changed.
        if ($this->form) {
            $newPath = Str::of(PublicFormController::FILE_UPLOAD_PATH)->replace('?', $this->form->id);
            if (Storage::exists($newPath.'/'.$value)) {
                return true;
            }
        }

        $fileNameParser = StorageFileNameParser::parse($value);
        if (! $uuid = $fileNameParser->uuid) {
            return false;
        }

        $filePath = PublicFormController::TMP_FILE_UPLOAD_PATH.$uuid;
        if (! Storage::exists($filePath)) {
            return false;
        }

        if (Storage::size($filePath) > $this->maxSize) {
            $this->error = 'File is too large.';

            return false;
        }

        if (count($this->fileTypes) > 0) {
            $this->error = 'Incorrect file type. Allowed only: '.implode(',', $this->fileTypes);
            return collect($this->fileTypes)->map(function ($type) {
                return strtolower($type);
            })->contains(strtolower($fileNameParser->extension));
        }

        return true;
    }

    public function message(): string
    {
        return $this->error;
    }
}
