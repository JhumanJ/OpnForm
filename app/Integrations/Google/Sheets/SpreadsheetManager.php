<?php

namespace App\Integrations\Google\Sheets;

use App\Integrations\Data\SpreadsheetData;
use App\Integrations\Google\Google;
use App\Models\Forms\Form;
use App\Models\Integration\FormIntegration;
use App\Service\Forms\FormSubmissionFormatter;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateValuesRequest;
use Google\Service\Sheets\Spreadsheet;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Arr;

class SpreadsheetManager
{
    protected Sheets $driver;
    protected SpreadsheetData $data;

    public function __construct(
        protected Google $google,
        protected FormIntegration $integration
    ) {
        $this->driver = new Sheets($google->getClient());

        $this->data = empty($this->integration->data)
            ? new SpreadsheetData()
            : new SpreadsheetData(
                url: $this->integration->data->url,
                spreadsheet_id: $this->integration->data->spreadsheet_id,
                columns: array_map(
                    fn ($column) => (array) $column,
                    $this->integration->data->columns
                )
            );
    }

    protected function convertToArray(mixed $object): array
    {
        return is_scalar($object) || is_null($object)
            ? $object
            : $this->convertToArray((array) $object);
    }

    public function get(string $id): Spreadsheet
    {
        $spreadsheet = $this->driver
            ->spreadsheets
            ->get($id);

        return $spreadsheet;
    }

    public function create(Form $form): Spreadsheet
    {
        $body = new Spreadsheet([
            'properties' => [
                'title' => $form->title
            ]
        ]);

        $spreadsheet = $this->driver->spreadsheets->create($body);

        $this->data->url = $spreadsheet->spreadsheetUrl;
        $this->data->spreadsheet_id = $spreadsheet->spreadsheetId;
        $this->data->columns = [];

        $this->updateHeaders($spreadsheet->spreadsheetId);

        return $spreadsheet;
    }

    public function buildColumns(): array
    {
        $properties = $this->integration->form->properties;

        foreach ($properties as $property) {
            $key = Arr::first(
                array_keys($this->data->columns),
                fn (int $key) => $this->data->columns[$key]['id'] === $property['id']
            );

            $column = Arr::only($property, ['id', 'name']);

            if (!is_null($key)) {
                $this->data->columns[$key] = $column;
            } else {
                $this->data->columns[] = $column;
            }
        }

        $this->integration->update([
            'data' => $this->data,
        ]);

        return $this->data->columns;
    }

    public function updateHeaders(string $id): static
    {
        $columns = $this->buildColumns();

        $headers = array_map(
            fn ($column) => $column['name'],
            $columns
        );

        return $this->setHeaders($id, $headers);
    }

    protected function setHeaders(string $id, array $headers): static
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

    public function buildRow(array $submissionData): array
    {
        $formatter = (new FormSubmissionFormatter($this->integration->form, $submissionData))->outputStringsOnly();

        $fields = $formatter->getFieldsWithValue();

        return collect($this->data->columns)
            ->map(function (array $column) use ($fields) {
                $field = Arr::first($fields, fn ($field) => $field['id'] === $column['id']);

                return $field ? $field['value'] : '';
            })
            ->toArray();
    }

    public function submit(array $submissionData): static
    {
        $this->updateHeaders($this->data->spreadsheet_id);

        $row = $this->buildRow($submissionData);

        $this->addRow(
            $this->data->spreadsheet_id,
            $row
        );

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
