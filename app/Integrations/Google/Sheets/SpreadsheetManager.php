<?php

namespace App\Integrations\Google\Sheets;

use App\Integrations\Google\Google;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateValuesRequest;
use Google\Service\Sheets\Spreadsheet;
use Google\Service\Sheets\ValueRange;

class SpreadsheetManager
{
    protected Sheets $driver;

    public function __construct(
        Google $google
    ) {
        $this->driver = new Sheets($google->getClient());
    }

    public function get(string $id): Spreadsheet
    {
        $spreadsheet = $this->driver
        ->spreadsheets
        ->get($id);

        return $spreadsheet;
    }

    public function create(string $title): Spreadsheet
    {
        $body = new Spreadsheet([
            'properties' => [
                'title' => $title
            ]
        ]);

        $spreadsheet = $this->driver->spreadsheets->create($body);

        return $spreadsheet;
    }

    public function setHeaders(string $id, array $headers): static
    {
        $valueRange = new ValueRange([
            'values' => [$headers],
        ]);

        $valueRange->setRange(
            $this->buildRange($headers)
        );

        $body = new BatchUpdateValuesRequest([
            'valueInputOption' => 'RAW',
            'data' => [$valueRange]
        ]);

        $this->driver
            ->spreadsheets_values
            ->batchUpdate($id, $body);

        return $this;
    }

    public function addRow(string $id, array $values): static
    {
        $valueRange = new ValueRange([
            'values' => [$values],
        ]);

        $params = [
            'valueInputOption' => 'RAW',
        ];

        $this->driver
            ->spreadsheets_values
            ->append(
                $id,
                $this->buildRange($values),
                $valueRange,
                $params
            );

        return $this;
    }

    protected function buildRange(array $values): string
    {
        return "A1:" . chr(64 + count($values)) . "1";
    }
}
