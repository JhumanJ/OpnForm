<?php

namespace App\Rules;

use App\Http\Controllers\Forms\PublicFormController;
use App\Service\Storage\StorageFileNameParser;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageFile implements Rule
{
    public int $maxSize;

    public string $error = 'Invalid file.';

    /** @var string[] */
    public array $fileTypes;

    /**
     * @param  int  $maxSize
     * @param  string[]  $fileTypes
     */
    public function __construct(int $maxSize, array $fileTypes = [])
    {
        $this->maxSize = $maxSize;

        $this->fileTypes = $fileTypes;
    }

    /**
     * File can have 2 formats:
     * - file_name-{uuid}.{ext}
     * - {uuid}
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $fileNameParser = StorageFileNameParser::parse($value);
        if (!$uuid = $fileNameParser->uuid) {
            return false;
        }

        $filePath = PublicFormController::TMP_FILE_UPLOAD_PATH.$uuid;
        if (!Storage::exists($filePath)) {
            return false;
        }

        if (Storage::size($filePath) > $this->maxSize) {
            $this->error = 'File is too large.';
            return false;
        }

        if (count($this->fileTypes) > 0) {
            $this->error = 'Incorrect file type. Allowed only: '.implode(",", $this->fileTypes);
            return in_array($fileNameParser->extension, $this->fileTypes);
        }
        return true;
    }

    public function message(): string
    {
        return $this->error;
    }
}
