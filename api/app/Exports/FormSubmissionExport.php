<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FormSubmissionExport implements FromArray, WithHeadingRow
{
    protected array $submissionData;

    public function __construct(array $submissionData)
    {
        $headingRow = [];
        $contentRow = [];
        foreach ($submissionData as $i => $row) {
            if ($i == 0) {
                $headingRow[] = $this->cleanColumnNames(array_keys($row));
            }
            $contentRow[] = array_values($row);
        }

        $this->submissionData = [
            $headingRow,
            $contentRow,
        ];
    }

    private function cleanColumnNames(array $columnNames): array
    {
        return collect($columnNames)->map(function ($columnName) {
            return preg_replace('/\s\(.*\)/', '', $columnName);
        })->toArray();
    }

    public function array(): array
    {
        return $this->submissionData;
    }
}
