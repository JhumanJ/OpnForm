<?php

namespace App\Service\AI\Prompts\Form;

use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class GenerateFormFieldsPrompt extends Prompt
{
    protected float $temperature = 0.81;

    protected int $maxTokens = 3000;

    /**
     * The prompt template for generating forms
     */
    public const PROMPT_TEMPLATE = <<<'EOD'
        Generate form field(s) based on the description below.

        <field_description>
            {formPrompt}
        </field_description>

        <current_form_context>
        Form Title: {formTitle}
        Existing Fields: {existingFields}
        </current_form_context>
        
        Return an array of field objects that fit the context and complement the existing form structure. Consider the form's purpose and existing fields when generating new fields.

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
        No need for lines width to be complete. Don't abuse putting multiple fields on the same line if it doesn't make sense. For First name and Last name, it works well for instance.
        
        Field generation guidelines:
        - Choose the most appropriate field type based on the data being collected
        - Consider the existing form context and avoid duplicating fields
        - Use logical field names that clearly describe the data being collected
        - Add helpful placeholder text and help text for complex fields
        - Set appropriate validation (required fields where necessary)
        - Use appropriate width settings for better layout
        - For select/multi-select fields, provide relevant options based on the context
        - For number fields, consider if rating, scale, or slider would be more appropriate
        - For rich text fields, consider if multi-line, matrix, or barcode input would be useful
        
        Return an array of field objects that:
        - Match the field description requirements
        - Complement the existing form structure
        - Use appropriate field types and configurations
        - Include proper validation and help text
        - Follow the form's overall purpose and flow
    EOD;

    /**
     * JSON schema for form output
     */
    protected ?array $jsonSchema = [
        'type' => 'object',
        'required' => ['properties'],
        'additionalProperties' => false,
        'properties' => [
            'properties' => [
                'type' => 'array',
                'description' => 'Array of form fields and elements',
                'items' => [
                    'anyOf' => [
                        ['$ref' => '#/definitions/titleProperty'],
                        ['$ref' => '#/definitions/richTextProperty'],
                        ['$ref' => '#/definitions/dateProperty'],
                        ['$ref' => '#/definitions/urlProperty'],
                        ['$ref' => '#/definitions/phoneNumberProperty'],
                        ['$ref' => '#/definitions/emailProperty'],
                        ['$ref' => '#/definitions/checkboxProperty'],
                        ['$ref' => '#/definitions/selectProperty'],
                        ['$ref' => '#/definitions/multiSelectProperty'],
                        ['$ref' => '#/definitions/numberProperty'],
                        ['$ref' => '#/definitions/filesProperty'],
                        ['$ref' => '#/definitions/nfTextProperty'],
                        ['$ref' => '#/definitions/nfPageBreakProperty'],
                        ['$ref' => '#/definitions/nfDividerProperty'],
                        ['$ref' => '#/definitions/nfImageProperty'],
                        ['$ref' => '#/definitions/nfCodeProperty']
                    ]
                ]
            ]
        ],
        'definitions' => FormFieldSchemas::FIELD_TYPE_DEFINITIONS
    ];

    public function __construct(
        public string $formPrompt,
        public string $formTitle = '',
        public array $existingFields = []
    ) {
        parent::__construct();
    }

    protected function getSystemMessage(): ?string
    {
        return 'You are an AI assistant specialized in generating form fields. Create appropriate field objects that fit the context and complement existing form structures. Focus on choosing the right field types and configurations based on the data being collected.';
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
     * Override getPromptVariables to handle complex formatting
     */
    protected function getPromptVariables(): array
    {
        $variables = parent::getPromptVariables();

        // Add the formatted existing fields
        $variables['{existingFields}'] = $this->formatExistingFields();

        return $variables;
    }

    /**
     * Format existing fields for display in the prompt
     */
    private function formatExistingFields(): string
    {
        if (empty($this->existingFields)) {
            return 'No existing fields';
        }

        $formatted = [];
        foreach ($this->existingFields as $field) {
            $fieldInfo = $field['name'] ?? 'Unnamed field';
            $fieldType = $field['type'] ?? 'Unknown type';
            $formatted[] = "- {$fieldInfo} ({$fieldType})";
        }

        return implode("\n", $formatted);
    }

    /**
     * Process the AI output to ensure it meets our requirements
     */
    public function processOutput(array $formFields): array
    {
        $newFormFields = $formFields['properties'] ?? [];

        // Process each field in the array
        foreach ($formFields['properties'] as $index => $field) {
            // Add unique identifiers to each field
            $newFormFields[$index]['id'] = Str::uuid()->toString();

            // Flatten core properties if they exist
            if (isset($field['core']) && is_array($field['core'])) {
                foreach ($field['core'] as $coreKey => $coreValue) {
                    $newFormFields[$index][$coreKey] = $coreValue;
                }
                // Remove the core property after flattening
                unset($newFormFields[$index]['core']);
            }
        }

        ray($newFormFields);

        return $newFormFields;
    }
}
