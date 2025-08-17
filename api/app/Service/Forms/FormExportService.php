<?php

namespace App\Service\Forms;

use App\Models\Forms\Form;
use App\Models\Forms\FormSubmission;
use App\Service\Forms\FormSubmissionFormatter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class FormExportService
{
    public const SYNC_EXPORT_THRESHOLD = 1000;
    public const CACHE_KEY_PREFIX = 'form_export_job';
    public const EXPORT_FILE_PATH = 'exports/temp/';

    /**
     * Determine if export should be processed synchronously or asynchronously
     */
    public function shouldExportAsync(Form $form): bool
    {
        return $form->submissions()->count() > self::SYNC_EXPORT_THRESHOLD;
    }

    /**
     * Format a single submission for export
     */
    public function formatSubmissionForExport(Form $form, FormSubmission $submission, array $displayColumns): array
    {
        $formatter = (new FormSubmissionFormatter($form, $submission->data))
            ->outputStringsOnly()
            ->setEmptyForNoValue()
            ->showRemovedFields()
            ->showHiddenFields()
            ->useSignedUrlForFiles();

        $formattedData = $formatter->getCleanKeyValue();
        $filteredData = ['id' => Hashids::encode($submission->id)];

        foreach ($displayColumns as $column => $value) {
            if ($value === true) {
                $key = collect($formattedData)->keys()->first(fn($key) => str_contains($key, $column));
                if ($key) {
                    $filteredData[$key] = $formattedData[$key];
                }
            }
        }

        if (isset($displayColumns['created_at']) && $displayColumns['created_at'] === true) {
            $filteredData['created_at'] = $submission->created_at->format('Y-m-d H:i');
        }

        return $filteredData;
    }

    /**
     * Generate CSV content from rows and upload to S3
     */
    public function generateAndUploadCsvFile(array $rows, string $fileName): string
    {
        if (empty($rows)) {
            throw new \Exception('No data to export');
        }

        // Generate CSV content
        $csvContent = $this->generateCsvContent($rows);

        // Upload to S3 with temporary path
        $filePath = self::EXPORT_FILE_PATH . $fileName;

        Storage::disk('s3')->put($filePath, $csvContent, [
            'visibility' => 'private',
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);

        // Generate a signed URL valid for 24 hours
        return Storage::disk('s3')->temporaryUrl($filePath, now()->addDay());
    }

    /**
     * Generate CSV content from array of rows
     */
    private function generateCsvContent(array $rows): string
    {
        if (empty($rows)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');

        // Write header row (clean column names)
        $headers = $this->cleanColumnNames(array_keys($rows[0]));
        fputcsv($output, $headers);

        // Write data rows
        foreach ($rows as $row) {
            fputcsv($output, array_values($row));
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * Clean column names by removing parenthetical content
     */
    private function cleanColumnNames(array $columnNames): array
    {
        return collect($columnNames)->map(function ($columnName) {
            return preg_replace('/\s\(.*\)/', '', $columnName);
        })->toArray();
    }

    /**
     * Generate a unique job ID for tracking
     */
    public function generateJobId(): string
    {
        return (string) Str::uuid();
    }

    /**
     * Generate cache key for export job
     */
    public function getCacheKey(string $jobId): string
    {
        return self::CACHE_KEY_PREFIX . ':' . $jobId;
    }
}
