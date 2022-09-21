<?php


namespace App\Service\Forms;

use App\Http\Requests\UserFormRequest;
use App\Http\Resources\FormResource;
use App\Models\Forms\Form;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Stevebauman\Purify\Facades\Purify;
use function App\Service\str_starts_with;
use function collect;

class FormCleaner
{
    /**
     * All the performed cleanings
     * @var bool
     */
    private array $cleanings = [];

    private array $data;

    private array $formDefaults = [
        'notifies' => false,
        'color' => '#3B82F6',
        'hide_title' => false,
        'no_branding' => false,
        'transparent_background' => false,
        'uppercase_labels' => true,
        'webhook_url' => null,
        'cover_picture' => null,
        'logo_picture' => null,
        'database_fields_update' => null,
        'theme' => 'default',
        'use_captcha' => false,
        'password' => null,
        'slack_webhook_url' => null,
    ];

    private array $fieldDefaults = [
        // 'name' => '' TODO: prevent name changing, use alias for column and keep original name as it is
        'hide_field_name' => false,
        'prefill' => null,
        'placeholder' => null,
        'help' => null,
        'file_upload' => false,
        'with_time' => null,
        'width' => 'full',
        'generates_uuid' => false,
        'generates_auto_increment_id' => false,
        'logic' => null,
        'allow_creation' => false
    ];

    private array $cleaningMessages = [
        // For form
        'notifies' => "Email notification were disabled.",
        'color' => "Form color set to default blue.",
        'hide_title' => "Title is not hidden.",
        'no_branding' => "OpenForm branding is not hidden.",
        'transparent_background' => "Transparent background was disabled.",
        'uppercase_labels' => "Labels use uppercase letters",
        'webhook_url' => "Webhook disabled.",
        'cover_picture' => 'The cover picture was removed.',
        'logo_picture' => 'The logo was removed.',
        'database_fields_update' => 'Form submission will only create new records (no updates).',
        'theme' => 'Default theme was applied.',
        'slack_webhook_url' => "Slack webhook disabled.",

        // For fields
        'hide_field_name' => 'Hide field name removed.',
        'prefill' => "Field prefill removed.",
        'placeholder' => "Empty text (placeholder) removed",
        'help' => "Help text removed.",
        'file_upload' => "Link field is not a file upload.",
        'with_time' => "Time was removed from date input.",
        'custom_block' => 'The custom block was removed.',
        'files' => 'The file upload file was hidden.',
        'relation' => 'The relation file was hidden.',
        'width' => 'The field width was set to full width',
        'allow_creation' => 'Select option creation was disabled.',

        // Advanced fields
        'generates_uuid' => 'ID generation disabled.',
        'generates_auto_increment_id' => 'ID generation disabled.',

        'use_captcha' => 'Captcha form protection was disabled.',

        // Security & Privacy
        'password' => 'Password protection was disabled',

        'logic' => 'Logic disabled for this property'
    ];

    /**
     * Returns form data after request ingestion
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Returns true if at least one cleaning was done
     * @return bool
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
    public function processForm(Request $request, Form $form) : FormCleaner {
        $data = (new FormResource($form))->toArray($request);
        $this->data = $this->commonCleaning($data);

        return $this;
    }

    private function isPro(Workspace $workspace) {
        return $workspace->is_pro;
    }

    /**
     * Dry run celanings
     * @param  User|null  $user
     */
    public function simulateCleaning(Workspace $workspace): FormCleaner {
        if($this->isPro($workspace)) return $this;

        $this->data = $this->removeProFeatures($this->data, true);

        return $this;
    }

    /**
     * Perform Cleanigns
     * @param  User|null  $user
     * @return $this|array
     */
    public function performCleaning(Workspace $workspace): FormCleaner
    {
        if($this->isPro($workspace)) return $this;

        $this->data = $this->removeProFeatures($this->data);

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
            // Remove pro custom blocks
            if (\Str::of($property['type'])->startsWith('nf-')) {
                $this->cleanings[$property['name']][] = 'custom_block';
                if (!$simulation) {
                    unset($data['properties'][$key]);
                }
                continue;
            }

            // Clean pro field options
            $this->cleanField($property, $this->fieldDefaults, $simulation);
        }
    }

    private function clean(array &$data, array $defaults, $simulation = false): void
    {
        foreach ($defaults as $key => $value) {
            if (Arr::get($data, $key) !== $value) {
                if (!isset($this->cleanings['form'])) $this->cleanings['form'] = [];
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
        foreach (['files'] as $proType) {
            if ($data['type'] == $proType && (!isset($data['hidden']) || !$data['hidden'])) {
                $this->cleanings[$data['name']][] = $proType;
                if (!$simulation) {
                    $data['hidden'] = true;
                }
            }
        }
    }

}
