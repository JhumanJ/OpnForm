<?php

namespace App\Integrations\Data;

use Spatie\LaravelData\Data;

class SpreadsheetData extends Data
{
    public function __construct(
        public string $url = '',
        public string $spreadsheet_id = '',
        public ?array $columns = []
    ) {
    }
}
