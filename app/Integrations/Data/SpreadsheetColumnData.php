<?php

namespace App\Integrations\Data;

class SpreadsheetColumnData
{
    public function __construct(
        public string $id,
        public string $name
    ) {
    }
}
