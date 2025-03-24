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
        Help me build the json structure for the form described below, be as accurate as possible.

        <form_description>
            {formPrompt}
        </form_description>
        
        Forms are represented as Json objects. There are several input types and layout block types (type start with nf-).
        You can use for instance nf-text to add a title or text to the form using some basic html (h1, p, b, i, u etc).
        Order of blocks matters.

        Available field types:
        - text: Text input (use multi_lines: true for multi-line text)
        - rich_text: Rich text input
        - date: Date picker (use with_time: true to include time selection)
        - url: URL input with validation
        - phone_number: Phone number input
        - email: Email input with validation
        - checkbox: Single checkbox for yes/no (use use_toggle_switch: true for toggle switch)
        - select: Dropdown selection (use without_dropdown: true for radio buttons, recommended for <5 options)
        - multi_select: Multiple selection (use without_dropdown: true for checkboxes, recommended for <5 options)
        - matrix: Matrix input with rows and columns
        - number: Numeric input
        - rating: Star rating 
        - scale: Numeric scale 
        - slider: Slider selection
        - files: File upload
        - signature: Signature pad
        - barcode: Barcode scanner
        - nf-text: Rich text content (not an input field)
        - nf-page-break: Page break for multi-page forms
        - nf-divider: Visual divider (not an input field)
        - nf-image: Image element
        - nf-code: Code block
        
        HTML formatting for nf-text:
        - Headers: <h1>, <h2> for section titles and subtitles
        - Text formatting: <b> or <strong> for bold, <i> or <em> for italic, <u> for underline, <s> for strikethrough
        - Links: <a href="url">link text</a> for hyperlinks
        - Lists: <ul><li>item</li></ul> for bullet lists, <ol><li>item</li></ol> for numbered lists
        - Colors: <span style="color: #hexcode">colored text</span> for colored text
        - Paragraphs: <p>paragraph text</p> for text blocks with spacing
        Use these HTML tags to create well-structured and visually appealing form content.
        
        Field width options:
        - width: "full" (default, takes entire width)
        - width: "1/2" (takes half width)
        - width: "1/3" (takes a third of the width)
        - width: "2/3" (takes two thirds of the width)
        - width: "1/4" (takes a quarter of the width)
        - width: "3/4" (takes three quarters of the width)
        Fields with width less than "full" will be placed on the same line if there's enough room. For example:
        - Two 1/2 width fields will be placed side by side
        - Three 1/3 width fields will be placed on the same line
        - etc.
        No need for lines width to be complete. Don't abuse putting multiple fields on the same line if it doens't make sense. For First name and Last name, it works well for instance.
        
        If the form is too long, you can paginate it by adding one or multiple page breaks (nf-page-break).
        
        Create a complete form with appropriate fields based on the description. Include:
        - A clear `title` (internal for form admin)
        - `nf-text` blocks to add a title or text to the form using some basic html (h1, p, b, i, u etc)
        - Logical field grouping
        - Required fields where necessary
        - Help text for complex fields
        - Appropriate validation
        - Customized submission text
    EOD;

    /**
     * JSON schema for form output
     */
    protected ?array $jsonSchema = [
        'type' => 'object',
        'required' => ['title', 'properties', 're_fillable', 'use_captcha', 'redirect_url', 'submitted_text', 'uppercase_labels', 'submit_button_text', 're_fill_button_text', 'color'],
        'additionalProperties' => false,
        'properties' => [
            'title' => [
                'type' => 'string',
                'description' => 'The title of the form (default: "New Form")'
            ],
            're_fillable' => [
                'type' => 'boolean',
                'description' => 'Whether the form can be refilled after submission (default: false)'
            ],
            'use_captcha' => [
                'type' => 'boolean',
                'description' => 'Whether to use CAPTCHA for spam protection (default: false)'
            ],
            'redirect_url' => [
                'type' => ['string', 'null'],
                'description' => 'URL to redirect to after submission (default: null)'
            ],
            'submitted_text' => [
                'type' => 'string',
                'description' => 'Text to display after form submission (default: "<p>Thank you for your submission!</p>")'
            ],
            'uppercase_labels' => [
                'type' => 'boolean',
                'description' => 'Whether to display field labels in uppercase (default: false)'
            ],
            'submit_button_text' => [
                'type' => 'string',
                'description' => 'Text for the submit button (default: "Submit")'
            ],
            're_fill_button_text' => [
                'type' => 'string',
                'description' => 'Text for the refill button (default: "Fill Again")'
            ],
            'color' => [
                'type' => 'string',
                'description' => 'Primary color for the form (default: "#64748b")'
            ],
            'properties' => [
                'type' => 'array',
                'description' => 'Array of form fields and elements',
                'items' => [
                    'anyOf' => [
                        ['$ref' => '#/definitions/textProperty'],
                        ['$ref' => '#/definitions/richTextProperty'],
                        ['$ref' => '#/definitions/dateProperty'],
                        ['$ref' => '#/definitions/urlProperty'],
                        ['$ref' => '#/definitions/phoneNumberProperty'],
                        ['$ref' => '#/definitions/emailProperty'],
                        ['$ref' => '#/definitions/checkboxProperty'],
                        ['$ref' => '#/definitions/selectProperty'],
                        ['$ref' => '#/definitions/multiSelectProperty'],
                        ['$ref' => '#/definitions/matrixProperty'],
                        ['$ref' => '#/definitions/numberProperty'],
                        ['$ref' => '#/definitions/ratingProperty'],
                        ['$ref' => '#/definitions/scaleProperty'],
                        ['$ref' => '#/definitions/sliderProperty'],
                        ['$ref' => '#/definitions/filesProperty'],
                        ['$ref' => '#/definitions/signatureProperty'],
                        ['$ref' => '#/definitions/barcodeProperty'],
                        ['$ref' => '#/definitions/nfTextProperty'],
                        ['$ref' => '#/definitions/nfPageBreakProperty'],
                        ['$ref' => '#/definitions/nfDividerProperty'],
                        ['$ref' => '#/definitions/nfImageProperty'],
                        ['$ref' => '#/definitions/nfCodeProperty']
                    ]
                ]
            ]
        ],
        'definitions' => [
            'option' => [
                'type' => 'object',
                'required' => ['name', 'id'],
                'additionalProperties' => false,
                'properties' => [
                    'name' => ['type' => 'string'],
                    'id' => ['type' => 'string']
                ]
            ],
            'selectOptions' => [
                'type' => 'object',
                'required' => ['options'],
                'additionalProperties' => false,
                'properties' => [
                    'options' => [
                        'type' => 'array',
                        'items' => ['$ref' => '#/definitions/option'],
                        'description' => 'Options for select fields'
                    ]
                ]
            ],
            'baseProperty' => [
                'type' => 'object',
                'required' => ['name', 'help', 'hidden', 'required', 'placeholder', 'width'],
                'additionalProperties' => false,
                'properties' => [
                    'name' => [
                        'type' => 'string',
                        'description' => 'The name/label of the field'
                    ],
                    'help' => [
                        'type' => 'string',
                        'description' => 'Help text for the field (default: null)'
                    ],
                    'hidden' => [
                        'type' => 'boolean',
                        'description' => 'Whether the field is hidden (default: false)'
                    ],
                    'required' => [
                        'type' => 'boolean',
                        'description' => 'Whether the field is required (default: false)'
                    ],
                    'placeholder' => [
                        'type' => 'string',
                        'description' => 'Placeholder text for the field. Leave empty if not needed (default: "")'
                    ],
                    'width' => [
                        'type' => 'string',
                        'enum' => ['full', '1/2', '1/3', '2/3', '1/4', '3/4'],
                        'description' => 'Width of the field in the form layout. "full" takes the entire width, "1/2" takes half width, "1/3" takes a third, etc. (default: "full")',
                    ]
                ]
            ],
            'textProperty' => [
                'type' => 'object',
                'required' => ['type', 'core', 'multi_lines', 'generates_uuid', 'max_char_limit', 'hide_field_name', 'show_char_limit'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['text']],
                    'multi_lines' => [
                        'type' => 'boolean',
                        'description' => 'Whether the text field should have multiple lines (default: false)',
                    ],
                    'generates_uuid' => [
                        'type' => 'boolean',
                        'description' => 'Whether the field should generate a UUID (default: false)'
                    ],
                    'max_char_limit' => [
                        'type' => ['integer', 'string'],
                        'description' => 'Maximum character limit for text fields (default: 500)'
                    ],
                    'hide_field_name' => [
                        'type' => 'boolean',
                        'description' => 'Whether to hide the field name (default: false)'
                    ],
                    'show_char_limit' => [
                        'type' => 'boolean',
                        'description' => 'Whether to show the character limit (default: false)'
                    ],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                ]
            ],
            'richTextProperty' => [
                'type' => 'object',
                'required' => ['type', 'core', 'max_char_limit'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['rich_text']],
                    'max_char_limit' => [
                        'type' => ['integer', 'string'],
                        'description' => 'Maximum character limit for rich text fields (default: 1000)'
                    ],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                ]
            ],
            'dateProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'with_time'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['date']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'with_time' => [
                        'type' => 'boolean',
                        'description' => 'Whether to include time selection with the date (default: false)',
                    ]
                ]
            ],
            'urlProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'max_char_limit'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['url']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'max_char_limit' => [
                        'type' => ['integer', 'string'],
                        'description' => 'Maximum character limit for URL fields (default: 500)'
                    ]
                ]
            ],
            'phoneNumberProperty' => [
                'type' => 'object',
                'required' => ['core', 'type'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['phone_number']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                ]
            ],
            'emailProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'max_char_limit'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['email']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'max_char_limit' => [
                        'type' => ['integer', 'string'],
                        'description' => 'Maximum character limit for email fields (default: 320)'
                    ]
                ]
            ],
            'checkboxProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'use_toggle_switch'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['checkbox']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'use_toggle_switch' => [
                        'type' => 'boolean',
                        'description' => 'Whether to display the checkbox as a toggle switch (default: false)',
                    ]
                ]
            ],
            'selectProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'select', 'without_dropdown'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['select']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'select' => ['$ref' => '#/definitions/selectOptions'],
                    'without_dropdown' => [
                        'type' => 'boolean',
                        'description' => 'Whether to display select options as radio buttons instead of a dropdown using FlatSelectInput. Recommended for small choices (<5 options) (default: false)',
                    ]
                ]
            ],
            'multiSelectProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'multi_select', 'without_dropdown'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['multi_select']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'multi_select' => ['$ref' => '#/definitions/selectOptions'],
                    'without_dropdown' => [
                        'type' => 'boolean',
                        'description' => 'Whether to display multi-select options as checkboxes instead of a dropdown using FlatSelectInput. Recommended for small choices (<5 options) (default: false)',
                    ]
                ]
            ],
            'matrixProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'rows', 'columns'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['matrix']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'rows' => [
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                        'description' => 'Rows for matrix fields (ex: ["Row 1"])'
                    ],
                    'columns' => [
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                        'description' => 'Columns for matrix fields (ex: ["1", "2", "3"])'
                    ]
                ]
            ],
            'numberProperty' => [
                'type' => 'object',
                'required' => ['core', 'type'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['number']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                ]
            ],
            'ratingProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'rating_max_value'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['rating']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'rating_max_value' => [
                        'type' => 'integer',
                        'description' => 'Maximum rating for rating fields (default: 5)'
                    ]
                ]
            ],
            'scaleProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'scale_min_value', 'scale_max_value', 'scale_step_value'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['scale']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'scale_min_value' => [
                        'type' => 'integer',
                        'description' => 'Minimum value for scale fields (default: 1)'
                    ],
                    'scale_max_value' => [
                        'type' => 'integer',
                        'description' => 'Maximum value for scale fields (default: 5)'
                    ],
                    'scale_step_value' => [
                        'type' => 'integer',
                        'description' => 'Step value for scale fields (default: 1)'
                    ]
                ]
            ],
            'sliderProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'slider_min_value', 'slider_max_value', 'slider_step_value'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['slider']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'slider_min_value' => [
                        'type' => 'integer',
                        'description' => 'Minimum value for slider fields (default: 0)'
                    ],
                    'slider_max_value' => [
                        'type' => 'integer',
                        'description' => 'Maximum value for slider fields (default: 50)'
                    ],
                    'slider_step_value' => [
                        'type' => 'integer',
                        'description' => 'Step value for slider fields (default: 1)'
                    ]
                ]
            ],
            'filesProperty' => [
                'type' => 'object',
                'required' => ['core', 'type'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['files']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                ]
            ],
            'signatureProperty' => [
                'type' => 'object',
                'required' => ['core', 'type'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['signature']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                ]
            ],
            'barcodeProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'decoders'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['barcode']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'decoders' => [
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                        'description' => 'Decoders for barcode fields (default: ["ean_reader", "ean_8_reader"])'
                    ]
                ]
            ],
            'nfTextProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'content'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['nf-text']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'content' => [
                        'type' => 'string',
                        'description' => 'HTML content for text elements. Supports headers (<h1>, <h2>), formatting (<b>, <i>, <u>, <s>), links (<a>), lists (<ul>, <ol>), colors (<span style="color: #hexcode">), and paragraphs (<p>). Example: "<h1>Form Title</h1><p>Please fill out this form.</p>"'
                    ]
                ]
            ],
            'nfPageBreakProperty' => [
                'type' => 'object',
                'required' => ['core', 'type', 'next_btn_text', 'previous_btn_text'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['nf-page-break']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                    'next_btn_text' => [
                        'type' => 'string',
                        'description' => 'Text for the next button in page breaks (default: "Next")'
                    ],
                    'previous_btn_text' => [
                        'type' => 'string',
                        'description' => 'Text for the previous button in page breaks (default: "Previous")'
                    ]
                ]
            ],
            'nfDividerProperty' => [
                'type' => 'object',
                'required' => ['core', 'type'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['nf-divider']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                ]
            ],
            'nfImageProperty' => [
                'type' => 'object',
                'required' => ['core', 'type'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['nf-image']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                ]
            ],
            'nfCodeProperty' => [
                'type' => 'object',
                'required' => ['core', 'type'],
                'additionalProperties' => false,
                'properties' => [
                    'type' => ['type' => 'string', 'enum' => ['nf-code']],
                    'core' => ['$ref' => '#/definitions/baseProperty'],
                ]
            ]
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
        $formData = parent::execute();
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

                // Flatten core properties if they exist
                if (isset($property['core']) && is_array($property['core'])) {
                    foreach ($property['core'] as $coreKey => $coreValue) {
                        $formData['properties'][$index][$coreKey] = $coreValue;
                    }
                    // Remove the core property after flattening
                    unset($formData['properties'][$index]['core']);
                }
            }
        }

        // Clean title data
        if (isset($formData['title'])) {
            // Remove quotes if the title is enclosed in them
            $formData['title'] = preg_replace('/^["\'](.*)["\']$/', '$1', $formData['title']);
        }

        ray($formData);

        return $formData;
    }
}
