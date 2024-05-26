<?php

namespace App\Integrations\Contracts;

use Spatie\LaravelData\Contracts\BaseData;

interface IntegrationManager
{
    public function getData(): BaseData;
}
