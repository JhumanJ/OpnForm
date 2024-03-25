<?php

namespace App\Http\Requests;

use App\Models\Forms\Form;
use App\Rules\StorageFile;
use App\Rules\ValidHCaptcha;
use App\Rules\ValidPhoneInputRule;
use App\Rules\ValidUrl;
use App\Service\Forms\FormLogicPropertyResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AnswerFormRequest extends FormRequest
{
    public Form $form;

    protected array $requestRules = [];

    protected int $maxFileSize;

    public function __construct(Request $request)
    {
        $this->form = $request->form;
        $this->maxFileSize = $this->form->workspace->max_file_size;
    }

    private function getFieldMaxFileSize($fieldProps)
    {
        return array_key_exists('max_file_size', $fieldProps) ?
            min($fieldProps['max_file_size'] * 1000000, $this->maxFileSize) : $this->maxFileSize;
    }

    /**
     * Validate form before use it
     *
     * @return bool
     */
    public function authorize()
    {
        return !$this->form->is_closed && !$this->form->max_number_of_submissions_reached && $this->form->visibility === 'public';
    }

    /**
     * Get the validation rules that apply to the form.
     *
     * @return array
     */
    public function rules()
    {
        foreach ($this->form->properties as $property) {
            $rules = [];

            /*if (!$this->form->is_pro) {  // If not pro then not check logic
                $property['logic'] = false;
            }*/

            // For get values instead of Id for select/multi select options
            $data = $this->toArray();
            $selectionFields = collect($this->form->properties)->filter(function ($pro) {
                return in_array($pro['type'], ['select', 'multi_select']);
            });
            foreach ($selectionFields as $field) {
                if (isset($data[$field['id']]) && is_array($data[$field['id']])) {
                    $data[$field['id']] = array_map(function ($val) use ($field) {
                        $tmpop = collect($field[$field['type']]['options'])->first(function ($op) use ($val) {
                            return $op['id'] ?? $op['value'] === $val;
                        });

                        return isset($tmpop['name']) ? $tmpop['name'] : '';
                    }, $data[$field['id']]);
                }
            }
            if (FormLogicPropertyResolver::isRequired($property, $data)) {
                $rules[] = 'required';

                if ($property['type'] == 'checkbox') {
                    // Required for checkboxes means true
                    $rules[] = 'accepted';
                } elseif ($property['type'] == 'rating') {
                    // For star rating, needs a minimum of 1 star
                    $rules[] = 'min:1';
                }
            } else {
                $rules[] = 'nullable';
            }

            // Clean id to escape "."
            $propertyId = $property['id'];
            if (in_array($property['type'], ['multi_select'])) {
                $rules[] = 'array';
                $this->requestRules[$propertyId . '.*'] = $this->getPropertyRules($property);
            } else {
                $rules = array_merge($rules, $this->getPropertyRules($property));
            }

            $this->requestRules[$propertyId] = $rules;
        }

        // Validate hCaptcha
        if ($this->form->use_captcha) {
            $this->requestRules['h-captcha-response'] = [new ValidHCaptcha()];
        }

        // Validate submission_id for edit mode
        if ($this->form->is_pro && $this->form->editable_submissions) {
            $this->requestRules['submission_id'] = 'string';
        }

        return $this->requestRules;
    }

    /**
     * Renames validated fields (because field names are ids)
     *
     * @return array
     */
    public function attributes()
    {
        $fields = [];
        foreach ($this->form->properties as $property) {
            $fields[$property['id']] = $property['name'];
        }

        return $fields;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [];
        foreach ($this->form->properties as $property) {
            if ($property['type'] == 'date' && isset($property['date_range']) && $property['date_range']) {
                $messages[$property['id'] . '.0.required_with'] = 'From date is required';
                $messages[$property['id'] . '.1.required_with'] = 'To date is required';
                $messages[$property['id'] . '.0.before_or_equal'] = 'From date must be before or equal To date';
            }
            if ($property['type'] == 'rating') {
                $messages[$property['id'] . '.min'] = 'A rating must be selected';
            }
        }

        return $messages;
    }

    /**
     * Return validation rules for a given form property
     */
    private function getPropertyRules($property): array
    {
        switch ($property['type']) {
            case 'text':
            case 'signature':
                return ['string'];
            case 'number':
            case 'rating':
            case 'scale':
            case 'slider':
                return ['numeric'];
            case 'select':
            case 'multi_select':
                if (($property['allow_creation'] ?? false)) {
                    return ['string'];
                }

                return [Rule::in($this->getSelectPropertyOptions($property))];
            case 'checkbox':
                return ['boolean'];
            case 'url':
                if (isset($property['file_upload']) && $property['file_upload']) {
                    $this->requestRules[$property['id'] . '.*'] = [new StorageFile($this->maxFileSize, [], $this->form)];

                    return ['array'];
                }

                return [new ValidUrl()];
            case 'files':
                $allowedFileTypes = [];
                if (!empty($property['allowed_file_types'])) {
                    $allowedFileTypes = explode(',', $property['allowed_file_types']);
                }
                $this->requestRules[$property['id'] . '.*'] = [new StorageFile($this->getFieldMaxFileSize($property), $allowedFileTypes, $this->form)];

                return ['array'];
            case 'email':
                return ['email:filter'];
            case 'date':
                if (isset($property['date_range']) && $property['date_range']) {
                    $this->requestRules[$property['id'] . '.*'] = $this->getRulesForDate($property);
                    $this->requestRules[$property['id'] . '.0'] = ['required_with:' . $property['id'] . '.1', 'before_or_equal:' . $property['id'] . '.1'];
                    $this->requestRules[$property['id'] . '.1'] = ['required_with:' . $property['id'] . '.0'];

                    return ['array', 'min:2'];
                }

                return $this->getRulesForDate($property);
            case 'phone_number':
                if (isset($property['use_simple_text_input']) && $property['use_simple_text_input']) {
                    return ['string'];
                }

                return ['string', 'min:6', new ValidPhoneInputRule()];
            default:
                return [];
        }
    }

    private function getRulesForDate($property)
    {
        if (isset($property['disable_past_dates']) && $property['disable_past_dates']) {
            return ['date', 'after_or_equal:today'];
        } elseif (isset($property['disable_future_dates']) && $property['disable_future_dates']) {
            return ['date', 'before_or_equal:today'];
        }

        return ['date'];
    }

    private function getSelectPropertyOptions($property): array
    {
        $type = $property['type'];
        if (!isset($property[$type])) {
            return [];
        }

        return array_column($property[$type]['options'], 'name');
    }

    protected function prepareForValidation()
    {
        $receivedData = $this->toArray();
        $mergeData = [];
        $countryCodeMapper = json_decode(file_get_contents(resource_path('data/country_code_mapper.json')), true);
        collect($this->form->properties)->each(function ($property) use ($countryCodeMapper, $receivedData, &$mergeData) {
            $receivedValue = $receivedData[$property['id']] ?? null;

            // Escape all '\' in select options
            if (in_array($property['type'], ['select', 'multi_select']) && !is_null($receivedValue)) {
                if (is_array($receivedValue)) {
                    $mergeData[$property['id']] = collect($receivedValue)->map(function ($value) {
                        $value = Str::of($value);

                        return $value->replace(
                            ["\e", "\f", "\n", "\r", "\t", "\v", '\\'],
                            ['\\e', '\\f', '\\n', '\\r', '\\t', '\\v', '\\\\']
                        )->toString();
                    })->toArray();
                } else {
                    $receivedValue = Str::of($receivedValue);
                    $mergeData[$property['id']] = $receivedValue->replace(
                        ["\e", "\f", "\n", "\r", "\t", "\v", '\\'],
                        ['\\e', '\\f', '\\n', '\\r', '\\t', '\\v', '\\\\']
                    )->toString();
                }
            }

            if ($property['type'] === 'phone_number' && (!isset($property['use_simple_text_input']) || !$property['use_simple_text_input']) && $receivedValue && in_array($receivedValue, $countryCodeMapper)) {
                $mergeData[$property['id']] = null;
            }
        });

        $this->merge($mergeData);
    }
}
