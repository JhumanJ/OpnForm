<?php

namespace App\Service\AI\Prompts\Form;

use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class GenerateFormPrompt extends Prompt
{
    protected float $temperature = 0.81;

    protected int $maxTokens = 3000;

    /**
     * The prompt template for generating forms
     */
    public const PROMPT_TEMPLATE = <<<'EOD'
        I need to build a form for: "{formPrompt}"
        
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
                        "placeholder": "Steve Jobs",
                        "multi_lines": false
                    },
                    {
                        "help": "We will never share your email with anyone else.",
                        "name": "Email",
                        "type": "email",
                        "hidden": false,
                        "required": true,
                        "placeholder": "steve@apple.com"
                    },
                    {
                      "help": null,
                      "name": "How would you rate your overall experience?",
                      "type": "select",
                      "hidden": false,
                      "select": {
                        "options": [
                            {"name": 1, "id": 1},
                            {"name": 2, "id": 2},
                            {"name": 3, "id": 3},
                            {"name": 4, "id": 4},
                            {"name": 5, "id": 5}
                        ]
                      },
                      "prefill": 5,
                      "required": true,
                      "placeholder": null
                    },
                    {
                        "help": null,
                        "name": "How can we help?",
                        "type": "text",
                        "hidden": false,
                        "required": true,
                        "multi_lines": true,
                        "placeholder": null,
                        "generates_uuid": false,
                        "max_char_limit": 2000,
                        "hide_field_name": false,
                        "show_char_limit": false
                    },
                    {
                        "help": "Upload any relevant files here.",
                        "name": "Have any attachments?",
                        "type": "files",
                        "hidden": false,
                        "placeholder": null
                    }
                ],
                "description": "<p>Looking for a real person to speak to?</p><p>We're here for you! Just drop in your queries below and we'll connect with you as soon as we can.</p>",
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
        
        Available field types:
        - text: Text input (use multi_lines: true for multi-line text)
        - rich_text: Rich text input
        - date: Date picker
        - url: URL input with validation
        - phone_number: Phone number input
        - email: Email input with validation
        - checkbox: Single checkbox for yes/no
        - select: Dropdown selection (requires select.options array)
        - multi_select: Multiple selection (requires multi_select.options array)
        - matrix: Matrix input with rows and columns
        - number: Numeric input
        - rating: Star rating (requires rating_max_value integer)
        - scale: Numeric scale (requires scale_min_value and scale_max_value integers)
        - slider: Slider selection (requires slider_min_value, slider_max_value, slider_step_value)
        - files: File upload
        - signature: Signature pad
        - barcode: Barcode scanner
        - nf-text: Rich text content (not an input field)
        - nf-page-break: Page break for multi-page forms
        - nf-divider: Visual divider (not an input field)
        - nf-image: Image element
        - nf-code: Code block
        
        For select and multi_select fields, use this format for options:
        ```json
        {
           "select": {
              "options": [
                 {"name": "Option 1", "id": "Option 1"},
                 {"name": "Option 2", "id": "Option 2"}
              ]
           }
        }
        ```
        
        If the form is too long, you can paginate it by adding a page break:
        ```json
        {
            "name": "Page Break",
            "next_btn_text": "Next",
            "previous_btn_text": "Previous",
            "type": "nf-page-break"
        }
        ```
        
        Create a complete form with appropriate fields based on the description. Include:
        - A clear title
        - A descriptive introduction using the description field
        - Logical field grouping
        - Required fields where necessary
        - Help text for complex fields
        - Appropriate validation
        - Customized submission text
        
        Reply with a valid JSON object containing the complete form structure.
    EOD;

    /**
     * JSON schema for form output
     */
    protected ?array $jsonSchema = [
        'type' => 'object',
        'required' => ['title', 'properties', 'description', 're_fillable', 'use_captcha', 'submitted_text', 'submit_button_text'],
        'properties' => [
            'title' => [
                'type' => 'string',
                'description' => 'The title of the form'
            ],
            'description' => [
                'type' => 'string',
                'description' => 'HTML description for the form'
            ],
            're_fillable' => [
                'type' => 'boolean',
                'description' => 'Whether the form can be refilled after submission'
            ],
            'use_captcha' => [
                'type' => 'boolean',
                'description' => 'Whether to use CAPTCHA for spam protection'
            ],
            'redirect_url' => [
                'type' => ['string', 'null'],
                'description' => 'URL to redirect to after submission'
            ],
            'submitted_text' => [
                'type' => 'string',
                'description' => 'Text to display after form submission'
            ],
            'uppercase_labels' => [
                'type' => 'boolean',
                'description' => 'Whether to display field labels in uppercase'
            ],
            'submit_button_text' => [
                'type' => 'string',
                'description' => 'Text for the submit button'
            ],
            're_fill_button_text' => [
                'type' => 'string',
                'description' => 'Text for the refill button'
            ],
            'color' => [
                'type' => 'string',
                'description' => 'Primary color for the form'
            ],
            'properties' => [
                'type' => 'array',
                'description' => 'Array of form fields and elements',
                'items' => [
                    'type' => 'object',
                    'required' => ['name', 'type'],
                    'anyOf' => [
                        {'$ref' => '#/definitions/textProperty'},
                        {'$ref' => '#/definitions/richTextProperty'},
                        {'$ref' => '#/definitions/dateProperty'},
                        {'$ref' => '#/definitions/urlProperty'},
                        {'$ref' => '#/definitions/phoneNumberProperty'},
                        {'$ref' => '#/definitions/emailProperty'},
                        {'$ref' => '#/definitions/checkboxProperty'},
                        {'$ref' => '#/definitions/selectProperty'},
                        {'$ref' => '#/definitions/multiSelectProperty'},
                        {'$ref' => '#/definitions/matrixProperty'},
                        {'$ref' => '#/definitions/numberProperty'},
                        {'$ref' => '#/definitions/ratingProperty'},
                        {'$ref' => '#/definitions/scaleProperty'},
                        {'$ref' => '#/definitions/sliderProperty'},
                        {'$ref' => '#/definitions/filesProperty'},
                        {'$ref' => '#/definitions/signatureProperty'},
                        {'$ref' => '#/definitions/barcodeProperty'},
                        {'$ref' => '#/definitions/nfTextProperty'},
                        {'$ref' => '#/definitions/nfPageBreakProperty'},
                        {'$ref' => '#/definitions/nfDividerProperty'},
                        {'$ref' => '#/definitions/nfImageProperty'},
                        {'$ref' => '#/definitions/nfCodeProperty'}
                    ]
                ]
            ]
        ],
        'definitions' => [
            'option' => [
                'type' => 'object',
                'required' => ['name', 'id'],
                'properties' => [
                    'name' => ['type' => ['string', 'number']],
                    'id' => ['type' => ['string', 'number']]
                ]
            ],
            'selectOptions' => [
                'type' => 'object',
                'required' => ['options'],
                'properties' => [
                    'options' => [
                        'type' => 'array',
                        'items' => {'$ref' => '#/definitions/option'}
                    ]
                ]
            ],
            'baseProperty' => [
                'type' => 'object',
                'required' => ['name', 'type'],
                'properties' => [
                    'name' => [
                        'type' => 'string',
                        'description' => 'The name/label of the field'
                    ],
                    'type' => [
                        'type' => 'string',
                        'enum' => [
                            'text', 'rich_text', 'date', 'url', 'phone_number', 'email', 
                            'checkbox', 'select', 'multi_select', 'matrix', 'number', 
                            'rating', 'scale', 'slider', 'files', 'signature', 'barcode',
                            'nf-text', 'nf-page-break', 'nf-divider', 'nf-image', 'nf-code'
                        ],
                        'description' => 'The type of the field'
                    ],
                    'help' => [
                        'type' => ['string', 'null'],
                        'description' => 'Help text for the field'
                    ],
                    'hidden' => [
                        'type' => 'boolean',
                        'description' => 'Whether the field is hidden'
                    ],
                    'required' => [
                        'type' => 'boolean',
                        'description' => 'Whether the field is required'
                    ],
                    'placeholder' => [
                        'type' => ['string', 'null'],
                        'description' => 'Placeholder text for the field'
                    ],
                    'prefill' => [
                        'type' => ['string', 'number', 'null'],
                        'description' => 'Prefilled value for the field'
                    ]
                ]
            ],
            'textProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'type' => ['enum' => ['text']],
                    'multi_lines' => [
                        'type' => 'boolean',
                        'description' => 'Whether the text field should have multiple lines'
                    ],
                    'generates_uuid' => [
                        'type' => 'boolean',
                        'description' => 'Whether the field should generate a UUID'
                    ],
                    'max_char_limit' => [
                        'type' => ['integer', 'string'],
                        'description' => 'Maximum character limit for text fields'
                    ],
                    'hide_field_name' => [
                        'type' => 'boolean',
                        'description' => 'Whether to hide the field name'
                    ],
                    'show_char_limit' => [
                        'type' => 'boolean',
                        'description' => 'Whether to show the character limit'
                    ]
                ]
            ],
            'richTextProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['rich_text']],
                    'max_char_limit' => [
                        'type' => ['integer', 'string'],
                        'description' => 'Maximum character limit for rich text fields'
                    ]
                ]
            ],
            'dateProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['date']]
                ]
            ],
            'urlProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['url']],
                    'max_char_limit' => [
                        'type' => ['integer', 'string'],
                        'description' => 'Maximum character limit for URL fields'
                    ]
                ]
            ],
            'phoneNumberProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['phone_number']]
                ]
            ],
            'emailProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['email']],
                    'max_char_limit' => [
                        'type' => ['integer', 'string'],
                        'description' => 'Maximum character limit for email fields'
                    ]
                ]
            ],
            'checkboxProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['checkbox']]
                ]
            ],
            'selectProperty' => [
                'type' => 'object',
                'required' => ['type', 'select'],
                'properties' => [
                    'type' => ['enum' => ['select']],
                    'select' => {'$ref' => '#/definitions/selectOptions'},
                    'without_dropdown' => [
                        'type' => 'boolean',
                        'description' => 'Whether to display select options as radio buttons instead of a dropdown'
                    ]
                ]
            },
            'multiSelectProperty' => [
                'type' => 'object',
                'required' => ['type', 'multi_select'],
                'properties' => [
                    'type' => ['enum' => ['multi_select']],
                    'multi_select' => {'$ref' => '#/definitions/selectOptions'}
                ]
            },
            'matrixProperty' => [
                'type' => 'object',
                'required' => ['type', 'rows', 'columns', 'selection_data'],
                'properties' => [
                    'type' => ['enum' => ['matrix']],
                    'rows' => [
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                        'description' => 'Rows for matrix fields'
                    ],
                    'columns' => [
                        'type' => 'array',
                        'items' => ['type' => ['string', 'number']],
                        'description' => 'Columns for matrix fields'
                    ],
                    'selection_data' => [
                        'type' => 'object',
                        'description' => 'Selection data for matrix fields'
                    ]
                ]
            ],
            'numberProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['number']]
                ]
            ],
            'ratingProperty' => [
                'type' => 'object',
                'required' => ['type', 'rating_max_value'],
                'properties' => [
                    'type' => ['enum' => ['rating']],
                    'rating_max_value' => [
                        'type' => 'integer',
                        'description' => 'Maximum rating for rating fields'
                    ]
                ]
            ],
            'scaleProperty' => [
                'type' => 'object',
                'required' => ['type', 'scale_min_value', 'scale_max_value', 'scale_step_value'],
                'properties' => [
                    'type' => ['enum' => ['scale']],
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
                    ]
                ]
            ],
            'sliderProperty' => [
                'type' => 'object',
                'required' => ['type', 'slider_min_value', 'slider_max_value', 'slider_step_value'],
                'properties' => [
                    'type' => ['enum' => ['slider']],
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
                    ]
                ]
            ],
            'filesProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['files']]
                ]
            ],
            'signatureProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['signature']]
                ]
            ],
            'barcodeProperty' => [
                'type' => 'object',
                'required' => ['type', 'decoders'],
                'properties' => [
                    'type' => ['enum' => ['barcode']],
                    'decoders' => [
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                        'description' => 'Decoders for barcode fields'
                    ]
                ]
            ],
            'nfTextProperty' => [
                'type' => 'object',
                'required' => ['type', 'content'],
                'properties' => [
                    'type' => ['enum' => ['nf-text']],
                    'content' => [
                        'type' => 'string',
                        'description' => 'HTML content for text elements'
                    ]
                ]
            ],
            'nfPageBreakProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['nf-page-break']],
                    'next_btn_text' => [
                        'type' => 'string',
                        'description' => 'Text for the next button in page breaks'
                    ],
                    'previous_btn_text' => [
                        'type' => 'string',
                        'description' => 'Text for the previous button in page breaks'
                    ]
                ]
            ],
            'nfDividerProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['nf-divider']]
                ]
            ],
            'nfImageProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['nf-image']]
                ]
            ],
            'nfCodeProperty' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => ['enum' => ['nf-code']]
                ]
            }
        ]
    ];

    public function __construct(
        public string $formPrompt
    ) {
        parent::__construct();
    }

    protected function getSystemMessage(): ?string
    {
        return 'You are an AI assistant specialized in creating form structures. Design intuitive, user-friendly forms that capture all necessary information based on the provided description.';
    }

    protected function getPromptTemplate(): string
    {
        return self::PROMPT_TEMPLATE;
    }

    /**
     * Override the execute method to automatically process the output
     */
    public function execute(): array
    {
        $this->initialize();
        $prompt = $this->buildPrompt();

        $this->completer->expectsJson();
        $this->completer->completeChat(
            [['role' => 'user', 'content' => $prompt]],
            $this->maxTokens,
            $this->temperature
        );

        $formData = $this->completer->getArray();
        return $this->processOutput($formData);
    }

    /**
     * Process the AI output to ensure it meets our requirements
     */
    public function processOutput(array $formData): array
    {
        // Add unique identifiers to properties
        if (isset($formData['properties']) && is_array($formData['properties'])) {
            foreach ($formData['properties'] as $index => $property) {
                // Add a unique ID to each property
                $formData['properties'][$index]['id'] = Str::uuid()->toString();
                
                // Fix types for specific field types
                if (isset($property['type'])) {
                    // Handle rating fields
                    if ($property['type'] === 'rating' && !isset($property['rating_max_value'])) {
                        $formData['properties'][$index]['rating_max_value'] = 5;
                    }
                    
                    // Handle scale fields
                    if ($property['type'] === 'scale') {
                        if (!isset($property['scale_min_value'])) {
                            $formData['properties'][$index]['scale_min_value'] = 1;
                        }
                        if (!isset($property['scale_max_value'])) {
                            $formData['properties'][$index]['scale_max_value'] = 5;
                        }
                        if (!isset($property['scale_step_value'])) {
                            $formData['properties'][$index]['scale_step_value'] = 1;
                        }
                    }
                    
                    // Handle slider fields
                    if ($property['type'] === 'slider') {
                        if (!isset($property['slider_min_value'])) {
                            $formData['properties'][$index]['slider_min_value'] = 0;
                        }
                        if (!isset($property['slider_max_value'])) {
                            $formData['properties'][$index]['slider_max_value'] = 50;
                        }
                        if (!isset($property['slider_step_value'])) {
                            $formData['properties'][$index]['slider_step_value'] = 1;
                        }
                    }
                    
                    // Handle barcode fields
                    if ($property['type'] === 'barcode' && !isset($property['decoders'])) {
                        $formData['properties'][$index]['decoders'] = ["ean_reader", "ean_8_reader"];
                    }
                    
                    // Handle matrix fields
                    if ($property['type'] === 'matrix') {
                        if (!isset($property['rows'])) {
                            $formData['properties'][$index]['rows'] = ["Row 1"];
                        }
                        if (!isset($property['columns'])) {
                            $formData['properties'][$index]['columns'] = [1, 2, 3];
                        }
                        if (!isset($property['selection_data'])) {
                            $formData['properties'][$index]['selection_data'] = [
                                "Row 1" => null
                            ];
                        }
                    }
                    
                    // Fix any incorrect textarea type to text with multi_lines
                    if ($property['type'] === 'textarea') {
                        $formData['properties'][$index]['type'] = 'text';
                        $formData['properties'][$index]['multi_lines'] = true;
                    }
                    
                    // Set default values for select/multi_select if not provided
                    if ($property['type'] === 'select' && !isset($property['select'])) {
                        $formData['properties'][$index]['select'] = [
                            'options' => [
                                ['name' => 'Option 1', 'id' => 'Option 1'],
                                ['name' => 'Option 2', 'id' => 'Option 2']
                            ]
                        ];
                    }
                    
                    if ($property['type'] === 'multi_select' && !isset($property['multi_select'])) {
                        $formData['properties'][$index]['multi_select'] = [
                            'options' => [
                                ['name' => 'Option 1', 'id' => 'Option 1'],
                                ['name' => 'Option 2', 'id' => 'Option 2']
                            ]
                        ];
                    }
                    
                    // Fix options format for select/multi_select
                    if ($property['type'] === 'select' && isset($property['select']['options'])) {
                        foreach ($property['select']['options'] as $optIndex => $option) {
                            if (isset($option['value']) && !isset($option['id'])) {
                                $formData['properties'][$index]['select']['options'][$optIndex]['id'] = $option['value'];
                                unset($formData['properties'][$index]['select']['options'][$optIndex]['value']);
                            }
                        }
                    }
                    
                    if ($property['type'] === 'multi_select' && isset($property['multi_select']['options'])) {
                        foreach ($property['multi_select']['options'] as $optIndex => $option) {
                            if (isset($option['value']) && !isset($option['id'])) {
                                $formData['properties'][$index]['multi_select']['options'][$optIndex]['id'] = $option['value'];
                                unset($formData['properties'][$index]['multi_select']['options'][$optIndex]['value']);
                            }
                        }
                    }
                    
                    // Ensure text fields have multi_lines property
                    if ($property['type'] === 'text' && !isset($property['multi_lines'])) {
                        $formData['properties'][$index]['multi_lines'] = false;
                    }
                    
                    // Ensure nf-text has content
                    if ($property['type'] === 'nf-text' && !isset($property['content'])) {
                        $formData['properties'][$index]['content'] = '<p>Text content</p>';
                    }
                }
                
                // Ensure all properties have required base fields
                if (!isset($property['hidden'])) {
                    $formData['properties'][$index]['hidden'] = false;
                }
                
                if (!isset($property['required']) && in_array($property['type'], ['text', 'rich_text', 'date', 'url', 'phone_number', 'email', 'checkbox', 'select', 'multi_select', 'matrix', 'number', 'rating', 'scale', 'slider', 'files', 'signature', 'barcode'])) {
                    $formData['properties'][$index]['required'] = false;
                }
            }
        }
        
        // Set default form-level properties if not provided
        if (!isset($formData['description'])) {
            $formData['description'] = '';
        }
        
        if (!isset($formData['re_fillable'])) {
            $formData['re_fillable'] = false;
        }
        
        if (!isset($formData['use_captcha'])) {
            $formData['use_captcha'] = false;
        }
        
        if (!isset($formData['submitted_text'])) {
            $formData['submitted_text'] = '<p>Thank you for your submission!</p>';
        }
        
        if (!isset($formData['submit_button_text'])) {
            $formData['submit_button_text'] = 'Submit';
        }
        
        if (!isset($formData['uppercase_labels'])) {
            $formData['uppercase_labels'] = false;
        }
        
        if (!isset($formData['re_fill_button_text'])) {
            $formData['re_fill_button_text'] = 'Fill Again';
        }
        
        // Clean title data
        if (isset($formData['title'])) {
            // Remove quotes if the title is enclosed in them
            $formData['title'] = preg_replace('/^["\'](.*)["\']$/', '$1', $formData['title']);
        }
        
        return $formData;
    }
}
