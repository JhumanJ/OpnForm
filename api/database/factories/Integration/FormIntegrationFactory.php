<?php

namespace Database\Factories\Integration;

use App\Models\Integration\FormIntegration;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormIntegrationFactory extends Factory
{
    protected $model = FormIntegration::class;

    public function definition()
    {
        return [
            'integration_id' => 'i_test',
            'status' => 'active',
            'logic' => [],
            'data' => [],
        ];
    }
}
