<?php

namespace App\Service\Forms;

use App\Http\Requests\UserFormRequest;
use App\Http\Resources\FormResource;
use App\Models\Forms\Form;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Stevebauman\Purify\Facades\Purify;

use function collect;

class FormCleaner
{
    /**
     * All the performed cleanings
     *
     * @var bool
     */
    private array $cleanings = [];

    private array $data;

    // For remove keys those have empty value
    private array $customKeys = ['seo_meta'];

    private array $formDefaults = [
        'no_branding' => false,
        'database_fields_update' => null,
        'editable_submissions' => false,
        'custom_code' => null,
        'seo_meta' => [],
        'redirect_url' => null,
        'enable_partial_submissions' => false,
    ];

    private array $formNonTrialingDefaults = [
        // Custom code protection disabled for now
        // 'custom_code' => null,
    ];

    private array $fieldDefaults = [
        // 'name' => '' TODO: prevent name changing, use alias for column and keep original name as it is
        'file_upload' => false,
    ];

    private array $cleaningMessages = [
        // For form
        'no_branding' => 'OpenForm branding is not hidden.',
        'database_fields_update' => 'Form submission will only create new records (no updates).',
        'editable_submissions' => 'Users will not be able to edit their submissions.',
        'custom_code' => 'Custom code was disabled',
        'seo_meta' => 'Custom SEO was disabled',
        'redirect_url' => 'Redirect Url was disabled',
        'enable_partial_submissions' => 'Partial submissions were disabled',

        // For fields
        'file_upload' => 'Link field is not a file upload.',
        'custom_block' => 'The custom block was removed.',
    ];

    /**
     * Returns form data after request ingestion
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Returns true if at least one cleaning was done
     */
    public function hasCleaned(): bool
    {
        return count($this->cleanings) > 0;
    }

    /**
     * Returns the messages for each cleaning step performed
     */
    public function getPerformedCleanings(): array
    {
        $cleaningMsgs = [];
        foreach ($this->cleanings as $key => $val) {
            $cleaningMsgs[$key] = collect($val)->map(function ($cleaning) {
                return $this->cleaningMessages[$cleaning];
            });
        }

        return $cleaningMsgs;
    }

    /**
     * Removes form pro features from data if user isn't pro
     */
    public function processRequest(UserFormRequest $request): FormCleaner
    {
        $data = $request->validated();
        $this->data = $this->commonCleaning($data);

        return $this;
    }

    /**
     * Create form cleaner instance from existing form
     */
    public function processForm(Request $request, Form $form): FormCleaner
    {
        $data = (new FormResource($form))->toArray($request);
        $this->data = $this->commonCleaning($data);

        return $this;
    }

    private function isPro(Workspace $workspace)
    {
        return $workspace->is_pro;
    }

    private function isTrialing(Workspace $workspace)
    {
        return $workspace->is_trialing;
    }
    /**
     * Dry run celanings
     *
     * @param  User|null  $user
     */
    public function simulateCleaning(Workspace $workspace): FormCleaner
    {
        if ($this->isTrialing($workspace)) {
            $this->data = $this->removeNonTrialingFeatures($this->data, true);
        }

        if (!$this->isPro($workspace)) {
            $this->data = $this->removeProFeatures($this->data, true);
        }

        return $this;
    }

    /**
     * Perform Cleanigns
     *
     * @param  User|null  $user
     * @return $this|array
     */
    public function performCleaning(Workspace $workspace): FormCleaner
    {
        if ($this->isTrialing($workspace)) {
            $this->data = $this->removeNonTrialingFeatures($this->data, true);
        }

        if (!$this->isPro($workspace)) {
            $this->data = $this->removeProFeatures($this->data);
        }

        return $this;
    }

    /**
     * Clean all forms:
     * - Escape html of custom text block
     */
    private function commonCleaning(array $data)
    {
        foreach ($data['properties'] as &$property) {
            if ($property['type'] == 'nf-text' && isset($property['content'])) {
                $property['content'] = Purify::clean($property['content']);
            }
        }

        return $data;
    }

    private function removeNonTrialingFeatures(array $data, $simulation = false)
    {
        $this->clean($data, $this->formNonTrialingDefaults);
        return $data;
    }

    private function removeProFeatures(array $data, $simulation = false)
    {
        $this->cleanForm($data, $simulation);
        $this->cleanProperties($data, $simulation);

        return $data;
    }

    private function cleanForm(array &$data, $simulation = false): void
    {
        $this->clean($data, $this->formDefaults, $simulation);
    }

    private function cleanProperties(array &$data, $simulation = false): void
    {
        foreach ($data['properties'] as $key => &$property) {
            /*
            // Remove pro custom blocks
            if (\Str::of($property['type'])->startsWith('nf-')) {
                $this->cleanings[$property['name']][] = 'custom_block';
                if (!$simulation) {
                    unset($data['properties'][$key]);
                }
                continue;
            }

            // Remove logic
            if (($property['logic']['conditions'] ?? null) != null || ($property['logic']['actions'] ?? []) != []) {
                $this->cleanings[$property['name']][] = 'logic';
                if (!$simulation) {
                    unset($data['properties'][$key]['logic']);
                }
            }
            */

            // Clean pro field options
            $this->cleanField($property, $this->fieldDefaults, $simulation);
        }
    }

    private function clean(array &$data, array $defaults, $simulation = false): void
    {
        foreach ($defaults as $key => $value) {

            // Get value from form
            $formVal = Arr::get($data, $key);

            // Transform customkeys values
            $formVal = $this->cleanCustomKeys($key, $formVal);

            // Transform boolean values
            $formVal = (($formVal === 0 || $formVal === '0') ? false : $formVal);
            $formVal = (($formVal === 1 || $formVal === '1') ? true : $formVal);

            if (!is_null($formVal) && $formVal !== $value) {
                if (!isset($this->cleanings['form'])) {
                    $this->cleanings['form'] = [];
                }
                $this->cleanings['form'][] = $key;

                // If not a simulation, do the cleaning
                if (!$simulation) {
                    Arr::set($data, $key, $value);
                }
            }
        }
    }

    private function cleanField(array &$data, array $defaults, $simulation = false): void
    {
        foreach ($defaults as $key => $value) {
            if (isset($data[$key]) && Arr::get($data, $key) !== $value) {
                $this->cleanings[$data['name']][] = $key;
                if (!$simulation) {
                    Arr::set($data, $key, $value);
                }
            }
        }

        // Remove pro types columns
        /*foreach (['files'] as $proType) {
            if ($data['type'] == $proType && (!isset($data['hidden']) || !$data['hidden'])) {
                $this->cleanings[$data['name']][] = $proType;
                if (!$simulation) {
                    $data['hidden'] = true;
                }
            }
        }*/
    }

    // Remove keys those have empty value
    private function cleanCustomKeys($key, $formVal)
    {
        if (in_array($key, $this->customKeys) && $formVal !== null) {
            $newVal = [];
            foreach ($formVal as $k => $val) {
                if ($val) {
                    $newVal[$k] = $val;
                }
            }

            return $newVal;
        }

        return $formVal;
    }
}
