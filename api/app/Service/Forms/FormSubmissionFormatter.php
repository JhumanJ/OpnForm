<?php

namespace App\Service\Forms;

use App\Models\Forms\Form;
use Carbon\Carbon;

class FormSubmissionFormatter
{
    /**
     * If true, creates html <a> links for emails and urls
     *
     * @var bool
     */
    private $createLinks = false;

    /**
     * If true, serialize arrays
     *
     * @var bool
     */
    private $outputStringsOnly = false;

    private $showHiddenFields = false;

    private $setEmptyForNoValue = false;

    private $showRemovedFields = false;

    private $useSignedUrlForFiles = false;

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

    public function useSignedUrlForFiles()
    {
        $this->useSignedUrlForFiles = true;

        return $this;
    }

    public function getMatrixString(array $val): string
    {
        $parts = [];
        foreach ($val as $key => $value) {
            if ($key !== null && $value !== null) {
                $parts[] = "$key: $value";
            }
        }
        return implode(' | ', $parts);
    }

    /**
     * Return a nice "FieldName": "Field Response" array
     * - If createLink enabled, returns html link for emails and links
     * Used for CSV exports
     */
    public function getCleanKeyValue()
    {
        $data = $this->formData;

        $fields = collect($this->form->properties);
        $removeFields = collect($this->form->removed_properties)->map(function ($field) {
            return [
                ...$field,
                'removed' => true,
            ];
        });
        if ($this->showRemovedFields) {
            $fields = $fields->merge($removeFields);
        }
        $fields = $fields->filter(function ($field) {
            return !in_array($field['type'], ['nf-text', 'nf-code', 'nf-page-break', 'nf-divider', 'nf-image']);
        })->values();

        $returnArray = [];
        foreach ($fields as $field) {

            if (in_array($field['id'], ['nf-text', 'nf-code', 'nf-page-break', 'nf-divider', 'nf-image'])) {
                continue;
            }

            if ($field['removed'] ?? false) {
                $field['name'] = $field['name'] . ' (deleted)';
            }

            // Add ID to avoid name clashes
            $field['name'] = $field['name'] . ' (' . \Str::of($field['id']) . ')';

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
                $returnArray[$field['name']] = '<a href="' . $data[$field['id']] . '">' . $data[$field['id']] . '</a>';
            } elseif ($this->createLinks && $field['type'] == 'email') {
                $returnArray[$field['name']] = '<a href="mailto:' . $data[$field['id']] . '">' . $data[$field['id']] . '</a>';
            } elseif ($field['type'] == 'multi_select') {
                $val = $data[$field['id']];
                if ($this->outputStringsOnly && is_array($val)) {
                    $returnArray[$field['name']] = implode(', ', $val);
                } else {
                    $returnArray[$field['name']] = $val;
                }
            } elseif ($field['type'] == 'matrix' && is_array($data[$field['id']])) {
                $returnArray[$field['name']] = $this->getMatrixString($data[$field['id']]);
            } elseif (in_array($field['type'], ['files', 'signature'])) {
                if ($this->outputStringsOnly) {
                    $formId = $this->form->id;
                    $returnArray[$field['name']] = implode(
                        ', ',
                        collect($data[$field['id']])->filter(function ($file) {
                            return !is_null($file) && !empty($file);
                        })->map(function ($file) use ($formId) {
                            return $this->getFileUrl($formId, $file);
                        })->toArray()
                    );
                } else {
                    $formId = $this->form->id;
                    $returnArray[$field['name']] = collect($data[$field['id']])->map(function ($file) use ($formId) {
                        return [
                            'file_url' => $this->getFileUrl($formId, $file),
                            'file_name' => $file,
                        ];
                    });
                }
            } else {
                if (is_array($data[$field['id']]) && $this->outputStringsOnly) {
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
                $field['value'] = '<a href="' . $data[$field['id']] . '">' . $data[$field['id']] . '</a>';
            } elseif ($this->createLinks && $field['type'] == 'email') {
                $field['value'] = '<a href="mailto:' . $data[$field['id']] . '">' . $data[$field['id']] . '</a>';
            } elseif ($field['type'] == 'checkbox') {
                $field['value'] = $data[$field['id']] ? trans('validation.yes') : trans('validation.no');
            } elseif ($field['type'] == 'date') {
                $dateFormat = ($field['date_format'] ?? 'dd/MM/yyyy') == 'dd/MM/yyyy' ? 'd/m/Y' : 'm/d/Y';
                if (isset($field['with_time']) && $field['with_time']) {
                    $dateFormat .= (isset($field['time_format']) && $field['time_format'] == 24) ? ' H:i' : ' g:ia';
                }
                if (is_array($data[$field['id']])) {
                    $field['value'] = isset($data[$field['id']][1]) ? (new Carbon($data[$field['id']][0]))->format($dateFormat)
                        . ' - ' . (new Carbon($data[$field['id']][1]))->format($dateFormat) : (new Carbon($data[$field['id']][0]))->format($dateFormat);
                } else {
                    $field['value'] = (new Carbon($data[$field['id']]))->format($dateFormat);
                }
            } elseif ($field['type'] == 'multi_select') {
                $val = $data[$field['id']];
                if ($this->outputStringsOnly) {
                    $field['value'] = implode(', ', $val);
                } else {
                    $field['value'] = $val;
                }
            } elseif ($field['type'] == 'matrix') {
                $field['value'] = str_replace(' | ', "\n", $this->getMatrixString($data[$field['id']]));
            } elseif (in_array($field['type'], ['files', 'signature'])) {
                if ($this->outputStringsOnly) {
                    $formId = $this->form->id;
                    $field['value'] = implode(
                        ', ',
                        collect($data[$field['id']])->map(function ($file) use ($formId) {
                            return $this->getFileUrl($formId, $file);
                        })->toArray()
                    );
                    $field['email_data'] = collect($data[$field['id']])->map(function ($file) use ($formId) {
                        $splitText = explode('.', $file);

                        return [
                            'unsigned_url' => route('open.forms.submissions.file', [$formId, $file]),
                            'signed_url' => $this->getFileUrl($formId, $file),
                            'label' => \Str::limit($file, 20, '[...].' . end($splitText)),
                        ];
                    })->toArray();
                } else {
                    $formId = $this->form->id;
                    $field['value'] = collect($data[$field['id']])->map(function ($file) use ($formId) {
                        return [
                            'file_url' => $this->getFileUrl($formId, $file),
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

    private function initIdFormData()
    {
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

    private function getFileUrl($formId, $file)
    {
        try {
            return $this->useSignedUrlForFiles ? \URL::signedRoute(
                'open.forms.submissions.file',
                [$formId, $file]
            ) : route('open.forms.submissions.file', [$formId, $file]);
        } catch (\Exception $e) {
            throw $e;
            return null;
        }
    }
}
