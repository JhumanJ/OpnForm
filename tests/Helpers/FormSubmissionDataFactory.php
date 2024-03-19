<?php

namespace Tests\Helpers;

use App\Models\Forms\Form;
use Faker;

class FormSubmissionDataFactory
{
    private ?Faker\Generator $faker;

    public function __construct(private Form $form)
    {
        $this->faker = Faker\Factory::create();
    }

    public static function generateSubmissionData(Form $form, array $data = [])
    {
        return (new self($form))->createSubmissionData($data);
    }

    public function createSubmissionData($mergeData = [])
    {
        $data = [];

        // for all non-hidden fields in form, create some fake data
        collect($this->form->properties)->each(function ($property) use (&$data) {
            $value = null;
            switch ($property['type']) {
                case 'text':
                    $value = $this->faker->name();
                    break;
                case 'email':
                    $value = $this->faker->unique()->email();
                    break;
                case 'checkbox':
                    $value = $this->faker->randomElement([true, false]);
                    break;
                case 'number':
                    $value = $this->faker->numberBetween();
                    break;
                case 'rating':
                case 'scale':
                    $value = $this->faker->numberBetween(1, 5);
                    break;
                case 'slider':
                    $value = $this->faker->numberBetween(0, 50);
                    break;
                case 'url':
                    $value = $this->faker->url();
                    break;
                case 'phone_number':
                    $value = 'FR+33749119783';
                    break;
                case 'date':
                    $value = $this->faker->date();
                    break;
                case 'select':
                    $value = $this->generateSelectValue($property);
                    break;
                case 'multi_select':
                    $value = $this->generateMultiSelectValues($property);
                    break;
                case 'files':
                    $value = null; // TODO: Will do this in future
                    break;
            }
            $data[$property['id']] = $value;
        });

        return array_merge($data, $mergeData);
    }

    private function generateSelectValue($property)
    {
        $values = [];
        if (isset($property['select']['options']) && count($property['select']['options']) > 0) {
            $values = collect($property['select']['options'])->map(function ($option) {
                return $option['name'];
            })->toArray();
        }

        return ($values) ? $this->faker->randomElement($values) : null;
    }

    private function generateMultiSelectValues($property)
    {
        $values = [];
        if (isset($property['multi_select']['options']) && count($property['multi_select']['options']) > 0) {
            $values = collect($property['multi_select']['options'])->map(function ($option) {
                return $option['name'];
            })->toArray();
        }

        return ($values) ? $this->faker->randomElements(
            $values,
            $this->faker->numberBetween(1, count($values))
        ) : null;
    }
}
