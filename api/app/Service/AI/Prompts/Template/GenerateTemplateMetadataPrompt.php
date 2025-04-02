<?php

namespace App\Service\AI\Prompts\Template;

use App\Models\Template;
use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class GenerateTemplateMetadataPrompt extends Prompt
{
    protected float $temperature = 0.81;

    protected int $maxTokens = 3000;

    /**
     * Available industries and types loaded from the database
     */
    protected array $availableIndustries = [];
    protected array $availableTypes = [];

    /**
     * The prompt template for generating template metadata
     */
    public const PROMPT_TEMPLATE = <<<'EOD'
        I need to generate metadata for a form template about: "{templatePrompt}"
        
        Please generate the following information for this template:
        
        1. SHORT DESCRIPTION:
        Create a single-sentence description that is concise, clearly explains what the form is about, highlights the main purpose or benefit, and is suitable for OpnForm (a free-to-use form builder). Do not use quotation marks.
        
        2. DETAILED DESCRIPTION:
        Create detailed HTML content with the following structure:
        - A paragraph explaining what the template is about
        - A section with heading (h2) explaining why and when to use such a form
        - A section with heading (h2) explaining who the target audience is and why it's beneficial
        - A section with heading (h2) explaining that OpnForm is the best tool to build this form
        Use only h2, p, ul, and li HTML tags. Each section (except for the first) MUST start with an h2 tag.
        
        3. TITLE:
        Create a title that contains or ends with the word "template", is short and to the point, doesn't include quotes, is optimized for SEO, and clearly describes the purpose of the form.
        
        4. INDUSTRY CLASSIFICATION:
        Classify the template into appropriate industries (minimum 1, maximum 3 but only if very relevant) from this list: {availableIndustries}
        Order them by relevance (most relevant first).
        
        5. TYPE CLASSIFICATION:
        Classify the template into appropriate types (minimum 1, maximum 3 but only if very accurate) from this list: {availableTypes}
        Order them by relevance (most relevant first).
        
        6. IMAGE SEARCH QUERY:
        Provide a concise search query for Unsplash that would be visually representative of the form's purpose.
        
        7. Q&A CONTENT:
        Create 4 to 6 question and answer pairs covering:
        - The purpose and benefits of this form template
        - When and why to use this form
        - Who the target audience is
        - Why OpnForm is the best option to create this form
        
        Reply with a valid JSON object containing all these elements.
    EOD;

    /**
     * JSON schema for template metadata output
     */
    protected ?array $jsonSchema = null;

    public function __construct(
        public string $templatePrompt
    ) {
        // Load available industries and types from the database
        $this->loadAvailableOptions();

        parent::__construct();
        $this->buildJsonSchema();
    }

    /**
     * Load available industries and types from the Template model
     */
    protected function loadAvailableOptions(): void
    {
        $this->availableIndustries = Template::getAllIndustries()->pluck('slug')->toArray();
        $this->availableTypes = Template::getAllTypes()->pluck('slug')->toArray();
    }

    /**
     * Dynamically build the JSON schema with enums for industries and types
     */
    protected function buildJsonSchema(): void
    {
        $this->jsonSchema = [
            'type' => 'object',
            'properties' => [
                'short_description' => [
                    'type' => 'string',
                    'description' => 'A concise single-sentence description of the form template'
                ],
                'detailed_description' => [
                    'type' => 'string',
                    'description' => 'Detailed HTML content describing the form template'
                ],
                'title' => [
                    'type' => 'string',
                    'description' => 'The title of the form template'
                ],
                'industries' => [
                    'type' => 'array',
                    'description' => 'List of industry slugs for the template',
                    'items' => [
                        'type' => 'string',
                        'enum' => $this->availableIndustries
                    ]
                ],
                'types' => [
                    'type' => 'array',
                    'description' => 'List of type slugs for the template',
                    'items' => [
                        'type' => 'string',
                        'enum' => $this->availableTypes
                    ]
                ],
                'image_search_query' => [
                    'type' => 'string',
                    'description' => 'Search query for Unsplash to find a relevant image'
                ],
                'qa_content' => [
                    'type' => 'array',
                    'description' => 'Q&A content for the template',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'question' => [
                                'type' => 'string',
                                'description' => 'The question about the form template'
                            ],
                            'answer' => [
                                'type' => 'string',
                                'description' => 'The answer to the question'
                            ]
                        ],
                        'required' => ['question', 'answer'],
                        'additionalProperties' => false
                    ]
                ]
            ],
            'required' => [
                'short_description',
                'detailed_description',
                'title',
                'industries',
                'types',
                'image_search_query',
                'qa_content'
            ],
            'additionalProperties' => false
        ];
    }

    protected function getSystemMessage(): ?string
    {
        return 'You are an assistant helping to generate comprehensive metadata for form templates. Create well-structured, informative content that explains the purpose, benefits, and target audience of the form template.';
    }

    protected function getPromptTemplate(): string
    {
        return self::PROMPT_TEMPLATE;
    }

    protected function buildPrompt(): string
    {
        $template = $this->getPromptTemplate();

        $industriesString = implode(', ', $this->availableIndustries);
        $typesString = implode(', ', $this->availableTypes);

        return Str::of($template)
            ->replace('{templatePrompt}', $this->templatePrompt)
            ->replace('{availableIndustries}', $industriesString)
            ->replace('{availableTypes}', $typesString)
            ->toString();
    }

    /**
     * Override the initialize method to ensure the options are loaded
     */
    protected function initialize(): void
    {
        if (empty($this->availableIndustries) || empty($this->availableTypes)) {
            throw new \InvalidArgumentException('Failed to load available industries and types from the database');
        }

        parent::initialize();
    }
}
