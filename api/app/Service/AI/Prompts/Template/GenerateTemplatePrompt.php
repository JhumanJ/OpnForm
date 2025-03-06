<?php

namespace App\Service\AI\Prompts\Template;

use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class GenerateTemplatePrompt extends Prompt
{
    protected float $temperature = 0.81;

    protected int $maxTokens = 3000;

    /**
     * The prompt template for generating form structure
     */
    public const PROMPT_TEMPLATE = <<<'EOD'
        You are an AI assistant for OpnForm, a form builder and your job is to build a form for our user.

        Forms are represented as Json objects. Here's an example form:
        ```json
         {
                "title": "Contact Us",
                "properties": [
                    {
                        "name": "Title",
                        "type": "nf-text",
                        "content": "<h1>Contact Us</h1>"
                    },
                    {
                        "help": null,
                        "name": "What's your name?",
                        "type": "text",
                        "hidden": false,
                        "required": true,
                        "placeholder": "Steve Jobs"
                    },
                    {
                        "help": null,
                        "name": "What's your email?",
                        "type": "email",
                        "hidden": false,
                        "required": true,
                        "placeholder": "steve@apple.com"
                    },
                    {
                        "help": null,
                        "name": "What's your message?",
                        "type": "textarea",
                        "hidden": false,
                        "required": true,
                        "placeholder": "I'd like to know more about..."
                    },
                    {
                        "help": null,
                        "name": "Attachments",
                        "type": "files",
                        "hidden": false,
                        "placeholder": null
                    }
                ],
                "description": "<p>Looking for a real person to speak to?</p><p>We're here for you!  Just drop in your queries below and we'll connect with you as soon as we can.</p>",
                "re_fillable": false,
                "use_captcha": false,
                "redirect_url": null,
                "submitted_text": "<p>Great, we've received your message. We'll get back to you as soon as we can :)</p>",
                "uppercase_labels": false,
                "submit_button_text": "Submit",
                "re_fill_button_text": "Fill Again",
                "color": "#64748b"
            }
        ```
        The form properties can only have one of the following types: 'text', 'number', 'rating', 'scale','slider', 'select', 'multi_select', 'date', 'files', 'checkbox', 'url', 'email', 'phone_number', 'signature'.
        All form properties objects need to have the keys 'help', 'name', 'type', 'hidden', 'placeholder', 'prefill'.
        The placeholder property is optional (can be "null") and is used to display a placeholder text in the input field.
        The help property is optional (can be "null") and is used to display extra information about the field.

        For the type "select" and "multi_select", the input object must have a key "select" (or "multi_select") that's mapped to an object like this one:
        ```json
        {
           "options": [
              {"name": 1, "value": 1},
              {"name": 2, "value": 2},
              {"name": 3, "value": 3},
              {"name": 4, "value": 4}
           ]
        }
        ```

        For "rating" you can set the field property "rating_max_value" to set the maximum value of the rating.
        For "scale" you can set the field property "scale_min_value", "scale_max_value" and "scale_step_value" to set the minimum, maximum and step value of the scale.
        For "slider" you can set the field property "slider_min_value", "slider_max_value" and "slider_step_value" to set the minimum, maximum and step value of the slider.

        If the form is too long, you can paginate it by adding a page break block in the list of properties:
        ```json
        {
            "name":"Page Break",
            "next_btn_text":"Next",
            "previous_btn_text":"Previous",
            "type":"nf-page-break",
        }
        ```

        If you need to add more context to the form, you can add text blocks:
        ```json
        {
            "name":"My Text",
            "type":"nf-text",
            "content": "<p>This is a text block.</p>"
        }
        ```

        Give me the valid JSON object only, representing the following form: "[REPLACE]"
        Do not ask me for more information about required properties or types, only suggest me a form structure.
    EOD;

    /**
     * JSON schema for template generation output
     */
    protected ?array $jsonSchema = [
        'type' => 'object',
        'required' => ['title', 'properties'],
        'properties' => [
            'title' => [
                'type' => 'string',
                'description' => 'The title of the form'
            ],
            'properties' => [
                'type' => 'array',
                'description' => 'The form fields',
                'items' => [
                    'type' => 'object',
                    'required' => ['id', 'name', 'type'],
                    'properties' => [
                        'id' => [
                            'type' => 'string',
                            'description' => 'Unique identifier for the field'
                        ],
                        'name' => [
                            'type' => 'string',
                            'description' => 'Display name of the field'
                        ],
                        'type' => [
                            'type' => 'string',
                            'description' => 'Field type',
                            'enum' => [
                                'text',
                                'email',
                                'url',
                                'tel',
                                'number',
                                'date',
                                'time',
                                'textarea',
                                'select',
                                'multi_select',
                                'checkbox',
                                'radio',
                                'rating',
                                'scale',
                                'slider',
                                'file',
                                'signature',
                                'address'
                            ]
                        ],
                        'help' => [
                            'type' => 'string',
                            'description' => 'Help text for the field'
                        ],
                        'required' => [
                            'type' => 'boolean',
                            'description' => 'Whether the field is required'
                        ],
                        'placeholder' => [
                            'type' => 'string',
                            'description' => 'Placeholder text for the field'
                        ],
                        'default' => [
                            'type' => ['string', 'number', 'boolean', 'null'],
                            'description' => 'Default value for the field'
                        ],
                        'select' => [
                            'type' => 'object',
                            'description' => 'Options for select fields',
                            'properties' => [
                                'options' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'label' => ['type' => 'string'],
                                            'value' => ['type' => 'string']
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'multi_select' => [
                            'type' => 'object',
                            'description' => 'Options for multi-select fields',
                            'properties' => [
                                'options' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'label' => ['type' => 'string'],
                                            'value' => ['type' => 'string']
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'rating_max_value' => [
                            'type' => 'integer',
                            'description' => 'Maximum value for rating fields'
                        ],
                        'scale_min_value' => [
                            'type' => 'integer',
                            'description' => 'Minimum value for scale fields'
                        ],
                        'scale_max_value' => [
                            'type' => 'integer',
                            'description' => 'Maximum value for scale fields'
                        ],
                        'scale_step_value' => [
                            'type' => 'integer',
                            'description' => 'Step value for scale fields'
                        ],
                        'slider_min_value' => [
                            'type' => 'integer',
                            'description' => 'Minimum value for slider fields'
                        ],
                        'slider_max_value' => [
                            'type' => 'integer',
                            'description' => 'Maximum value for slider fields'
                        ],
                        'slider_step_value' => [
                            'type' => 'integer',
                            'description' => 'Step value for slider fields'
                        ],
                        'without_dropdown' => [
                            'type' => 'boolean',
                            'description' => 'Whether to display select options as radio buttons or checkboxes instead of a dropdown'
                        ]
                    ]
                ]
            ]
        ]
    ];

    public function __construct(
        public string $templatePrompt
    ) {
        parent::__construct();
    }

    protected function getSystemMessage(): ?string
    {
        return 'You are an assistant helping to generate forms.';
    }

    protected function getPromptTemplate(): string
    {
        return self::PROMPT_TEMPLATE;
    }

    protected function buildPrompt(): string
    {
        return Str::of($this->getPromptTemplate())
            ->replace('[REPLACE]', $this->templatePrompt)
            ->toString();
    }

    /**
     * Process the AI output to ensure it meets our requirements
     */
    public function processOutput(array $formData): array
    {
        // Add property uuids, improve form with options
        foreach ($formData['properties'] as &$property) {
            $property['id'] = Str::uuid()->toString(); // Column ID

            // Fix types
            if ($property['type'] == 'rating') {
                $property['rating_max_value'] = $property['rating_max_value'] ?? 5;
            } elseif ($property['type'] == 'scale') {
                $property['scale_min_value'] = $property['scale_min_value'] ?? 1;
                $property['scale_max_value'] = $property['scale_max_value'] ?? 5;
                $property['scale_step_value'] = $property['scale_step_value'] ?? 1;
            } elseif ($property['type'] == 'slider') {
                $property['slider_min_value'] = $property['slider_min_value'] ?? 0;
                $property['slider_max_value'] = $property['slider_max_value'] ?? 100;
                $property['slider_step_value'] = $property['slider_step_value'] ?? 1;
            }

            if (($property['type'] == 'select' && count($property['select']['options']) <= 4)
                || ($property['type'] == 'multi_select' && count($property['multi_select']['options']) <= 4)
            ) {
                $property['without_dropdown'] = true;
            }
        }

        // Clean data
        $formData['title'] = Str::of($formData['title'])->replace('"', '')->toString();

        return $formData;
    }
}
