<?php

namespace App\Http\Requests;

use App\Models\Forms\Form;

use App\Rules\StorageFile;
use App\Service\Forms\FormLogicPropertyResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Rules\ValidHCaptcha;

class AnswerFormRequest extends FormRequest
{
    const MAX_FILE_SIZE_PRO = 5000000;
    const MAX_FILE_SIZE_ENTERPRISE = 20000000;

    public Form $form;

    protected array $requestRules = [];
    protected int $maxFileSize;

    public function __construct(Request $request)
    {
        $this->form = $request->form;

        $this->maxFileSize = self::MAX_FILE_SIZE_PRO;
        $workspace = $this->form->workspace;
        if ($workspace && $workspace->is_enterprise) {
            $this->maxFileSize = self::MAX_FILE_SIZE_ENTERPRISE;
        }
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

            if (!$this->form->is_pro) {  // If not pro then not check logic
                $property['logic'] = false;
            }

            // For get values instead of Id for select/multi select options
            $data = $this->toArray();
            $selectionFields = collect($this->form->properties)->filter(function ($pro) {
                return in_array($pro['type'], ['select', 'multi_select']);
            });
            foreach ($selectionFields as $field){
                if(isset($data[$field['id']]) && is_array($data[$field['id']])){
                    $data[$field['id']] = array_map(function ($val) use ($field) {
                        $tmpop = collect($field[$field['type']]['options'])->first(function ($op) use ($val) {
                            return ($op['id'] === $val);
                        });
                        return isset($tmpop['name']) ? $tmpop['name'] : "";
                    }, $data[$field['id']]);
                }
            };
            if (FormLogicPropertyResolver::isRequired($property, $data)) {
                $rules[] = 'required';

                // Required for checkboxes means true
                if ($property['type'] == 'checkbox') {
                    $rules[] = 'accepted';
                }
            } else {
                $rules[] = 'nullable';
            }

            // Clean id to escape "."
            $propertyId = $property['id'];
            if (in_array($property['type'], ['multi_select'])) {
                $rules[] = 'array';
                $this->requestRules[$propertyId.'.*'] = $this->getPropertyRules($property);
            } else {
                $rules = array_merge($rules, $this->getPropertyRules($property));
            }

            $this->requestRules[$propertyId] = $rules;
        }

        // Validate hCaptcha
        if ($this->form->is_pro && $this->form->use_captcha) {
            $this->requestRules['h-captcha-response'] = [new ValidHCaptcha()];
        }
        return $this->requestRules;
    }

    /**
     * Renames validated fields (because field names are ids)
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
     * Return validation rules for a given form property
     * @param $property
     */
    private function getPropertyRules($property): array
    {
        switch ($property['type']) {
            case 'text':
            case 'phone_number':
            case 'signature':
                return ['string'];
            case 'number':
                return ['numeric'];
            case 'select':
            case 'multi_select':
                if ($this->form->is_pro && ($property['allow_creation'] ?? false)) {
                    return ['string'];
                }
                return [Rule::in($this->getSelectPropertyOptions($property))];
            case 'checkbox':
                return ['boolean'];
            case 'url':
                if (isset($property['file_upload']) && $property['file_upload']) {
                    $this->requestRules[$property['id'].'.*'] = [new StorageFile($this->maxFileSize)];
                    return ['array'];
                }
                return ['url'];
            case 'files':
                $allowedFileTypes = [];
                if($this->form->is_pro && !empty($property['allowed_file_types'])){
                    $allowedFileTypes = explode(",", $property['allowed_file_types']);
                }
                $this->requestRules[$property['id'].'.*'] = [new StorageFile($this->maxFileSize, $allowedFileTypes)];
                return ['array'];
            case 'email':
                return ['email:filter'];
            case 'date':
                if (isset($property['date_range']) && $property['date_range']) {
                    $this->requestRules[$property['id'].'.*'] = $this->getRulesForDate($property);
                    return ['array', 'min:2'];
                }
                return $this->getRulesForDate($property);
            default:
                return [];
        }
    }

    private function getRulesForDate($property)
    {
        if (isset($property['disable_past_dates']) && $property['disable_past_dates']) {
            return ['date', 'after_or_equal:today'];
        }else if (isset($property['disable_future_dates']) && $property['disable_future_dates']) {
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
        // Escape all '\' in select options
        $receivedData = $this->toArray();
        $mergeData = [];
        collect($this->form->properties)->filter(function ($property) {
            return in_array($property['type'], ['select', 'multi_select']);
        })->each(function ($property) use ($receivedData, &$mergeData) {
            $receivedValue = $receivedData[$property['id']] ?? null;
            if (!is_null($receivedValue)) {
                if (is_array($receivedValue)) {
                    $mergeData[$property['id']] = collect($receivedValue)->map(function ($value) {
                        $value = Str::of($value);
                        return $value->replace(
                            ["\e", "\f", "\n", "\r", "\t", "\v", "\\"],
                            ["\\e", "\\f", "\\n", "\\r", "\\t", "\\v", "\\\\"]
                        )->toString();
                    })->toArray();
                } else {
                    $receivedValue = Str::of($receivedValue);
                    $mergeData[$property['id']] = $receivedValue->replace(
                        ["\e", "\f", "\n", "\r", "\t", "\v", "\\"],
                        ["\\e", "\\f", "\\n", "\\r", "\\t", "\\v", "\\\\"]
                    )->toString();
                }
            }
        });

        $this->merge($mergeData);
    }
}
