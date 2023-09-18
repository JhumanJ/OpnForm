<?php


namespace App\Service\Forms;


use App\Models\Forms\Form;
use App\Service\WorkspaceHelper;
use Carbon\Carbon;

class FormSubmissionFormatter
{

    /**
     * If true, creates html <a> links for emails and urls
     * @var bool
     */
    private $createLinks = false;

    /**
     * If true, serialize arrays
     * @var bool
     */
    private $outputStringsOnly = false;

    private $showHiddenFields = false;

    private $setEmptyForNoValue = false;

    private $showRemovedFields = false;

    /**
     * Logic resolver needs an array id => value, so we create it here
     */
    private $idFormData = null;

    public function __construct(private Form $form, private array $formData)
    {
       $this->initIdFormData();
    }

    public function createLinks()
    {
        $this->createLinks = true;
        return $this;
    }

    public function showHiddenFields()
    {
        $this->showHiddenFields = true;
        return $this;
    }

    public function outputStringsOnly()
    {
        $this->outputStringsOnly = true;
        return $this;
    }

    public function setEmptyForNoValue()
    {
        $this->setEmptyForNoValue = true;
        return $this;
    }

    public function showRemovedFields()
    {
        $this->showRemovedFields = true;
        return $this;
    }

    /**
     * Return a nice "FieldName": "Field Response" array
     * - If createLink enabled, returns html link for emails and links
     * Used for CSV exports
     */
    public function getCleanKeyValue()
    {
        $data = $this->formData;
        $fields = ($this->showRemovedFields) ? array_merge($this->form->properties, $this->form->removed_properties) : $this->form->properties;

        $returnArray = [];
        foreach ($fields as &$field) {
            $isRemoved = in_array($field['id'], array_column($this->form->removed_properties, 'id')) ?? false;
            if($isRemoved){
                $field['name'] = $field['name']." (deleted)";
            }

            // If not present skip
            if (!isset($data[$field['id']])) {
                if ($this->setEmptyForNoValue) {
                    $returnArray[$field['name']] = '';
                }
                continue;
            }

            // If hide hidden fields
            if (!$this->showHiddenFields) {
                if (FormLogicPropertyResolver::isHidden($field, $this->idFormData ?? [])) {
                    continue;
                }
            }

            if ($this->createLinks && $field['type'] == 'url') {
                $returnArray[$field['name']] = '<a href="'.$data[$field['id']].'">'.$data[$field['id']].'</a>';
            } elseif ($this->createLinks && $field['type'] == 'email') {
                $returnArray[$field['name']] = '<a href="mailto:'.$data[$field['id']].'">'.$data[$field['id']].'</a>';
            } elseif ($field['type'] == 'multi_select') {
                $val = $data[$field['id']];
                if ($this->outputStringsOnly) {
                    $returnArray[$field['name']] = implode(', ', $val);
                } else {
                    $returnArray[$field['name']] = $val;
                }
            } elseif ($field['type'] == 'files') {
                if ($this->outputStringsOnly) {
                    $formId = $this->form->id;
                    $returnArray[$field['name']] = implode(', ',
                        collect($data[$field['id']])->map(function ($file) use ($formId) {
                            return route('open.forms.submissions.file', [$formId, $file]);
                        })->toArray()
                    );
                } else {
                    $formId = $this->form->id;
                    $returnArray[$field['name']] = collect($data[$field['id']])->map(function ($file) use ($formId) {
                            return [
                                'file_url' => route('open.forms.submissions.file', [$formId, $file]),
                                'file_name' => $file,
                            ];
                        });
                }
            } else {
                if (is_array($data[$field['id']])) {
                    $data[$field['id']] = implode(', ', $data[$field['id']]);
                }
                $returnArray[$field['name']] = $data[$field['id']];
            }
        }
        return $returnArray;
    }

    /**
     * Return a list of fields, with a filled value attribute.
     * Used for humans.
     */
    public function getFieldsWithValue()
    {
        $data = $this->formData;
        $fields = $this->form->properties;
        $transformedFields = [];
        foreach ($fields as $field) {
            if (!isset($field['id']) || !isset($data[$field['id']])) {
                continue;
            }

            // If hide hidden fields
            if (!$this->showHiddenFields) {
                if (FormLogicPropertyResolver::isHidden($field, $this->idFormData)) {
                    continue;
                }
            }

            if ($this->createLinks && $field['type'] == 'url') {
                $field['value'] = '<a href="'.$data[$field['id']].'">'.$data[$field['id']].'</a>';
            } elseif ($this->createLinks && $field['type'] == 'email') {
                $field['value'] = '<a href="mailto:'.$data[$field['id']].'">'.$data[$field['id']].'</a>';
            } elseif ($field['type'] == 'checkbox') {
                $field['value'] = $data[$field['id']] ? 'Yes' : 'No';
            } elseif ($field['type'] == 'date') {
                if (is_array($data[$field['id']])) {
                    $field['value'] = isset($data[$field['id']][1]) ? (new Carbon($data[$field['id']][0]))->format('d/m/Y')
                        .' - '.(new Carbon($data[$field['id']][1]))->format('d/m/Y') : (new Carbon($data[$field['id']][0]))->format('d/m/Y');
                } else {
                    $field['value'] = (new Carbon($data[$field['id']]))->format((isset($field['with_time']) && $field['with_time']) ? 'd/m/Y H:i' : 'd/m/Y');
                }
            } elseif ($field['type'] == 'multi_select') {
                $val = $data[$field['id']];
                if ($this->outputStringsOnly) {
                    $field['value'] = implode(', ', $val);
                } else {
                    $field['value'] = $val;
                }
            } elseif ($field['type'] == 'files') {
                if ($this->outputStringsOnly) {
                    $formId = $this->form->id;
                    $field['value'] = implode(', ',
                        collect($data[$field['id']])->map(function ($file) use ($formId) {
                            return route('open.forms.submissions.file', [$formId, $file]);
                        })->toArray()
                    );
                } else {
                    $formId = $this->form->id;
                    $field['value'] = collect($data[$field['id']])->map(function ($file) use ($formId) {
                        return [
                            'file_url' => route('open.forms.submissions.file', [$formId, $file]),
                            'file_name' => $file,
                        ];
                    });

                }
            } else {
                if (is_array($data[$field['id']]) && $this->outputStringsOnly) {
                    $field['value'] = implode(', ', $data[$field['id']]);
                } else {
                    $field['value'] = $data[$field['id']];
                }
            }
            $transformedFields[] = $field;
        }
        return $transformedFields;
    }

    private function initIdFormData() {
        $formProperties = collect($this->form->properties);
        foreach ($this->formData as $key => $value) {
            $property = $formProperties->first(function ($item) use ($key) {
                return $item['id'] == $key;
            });
            if ($property) {
                $this->idFormData[$property['id']] = $value;
            }
        }
    }

}
