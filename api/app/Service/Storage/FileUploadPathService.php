<?php

namespace App\Service\Storage;

use Illuminate\Support\Str;

class FileUploadPathService
{
    /**
     * Base path for form submission file uploads
     */
    private const FILE_UPLOAD_PATH = 'forms/?/submissions';

    /**
     * Base path for temporary file uploads
     */
    private const TMP_FILE_UPLOAD_PATH = 'tmp/';

    /**
     * Generate the file upload path for a specific form
     *
     * @param int|string $formId The form ID
     * @param string|null $fileName Optional filename to append
     * @return string The complete file upload path
     */
    public static function getFileUploadPath(int|string $formId, ?string $fileName = null): string
    {
        $path = Str::of(self::FILE_UPLOAD_PATH)->replace('?', $formId);

        if ($fileName) {
            $path = $path . '/' . $fileName;
        }

        return $path;
    }

    /**
     * Generate the temporary file upload path
     *
     * @param string|null $fileName Optional filename to append
     * @return string The complete temporary file upload path
     */
    public static function getTmpFileUploadPath(?string $fileName = null): string
    {
        $path = self::TMP_FILE_UPLOAD_PATH;

        if ($fileName) {
            $path = $path . $fileName;
        }

        return $path;
    }
}
