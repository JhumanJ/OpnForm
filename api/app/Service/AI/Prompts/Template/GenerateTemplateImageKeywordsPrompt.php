<?php

namespace App\Service\AI\Prompts\Template;

use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class GenerateTemplateImageKeywordsPrompt extends Prompt
{
    protected float $temperature = 0.7;

    protected int $maxTokens = 100;

    /**
     * The prompt template for generating image keywords
     */
    public const PROMPT_TEMPLATE = <<<'EOD'
        I need a relevant search query for Unsplash to find an appropriate cover image for a form template with this description:
        
        "{formDescription}"
        
        The search query should be concise and visually representative of the form's purpose.
        
        Reply only with a valid JSON object containing a "search_query" key, like this:
        ```json
        {
           "search_query": "your suggested search term here"
        }
        ```
    EOD;

    /**
     * JSON schema for image keywords output
     */
    protected ?array $jsonSchema = [
        'type' => 'object',
        'required' => ['search_query'],
        'properties' => [
            'search_query' => [
                'type' => 'string',
                'description' => 'Search query for Unsplash to find a relevant image'
            ]
        ]
    ];

    public function __construct(
        public string $formDescription
    ) {
        parent::__construct();
    }

    protected function getSystemMessage(): ?string
    {
        return 'You are an assistant helping to find relevant images for form templates. Suggest search terms that will yield appropriate, professional images that visually represent the form\'s purpose.';
    }

    protected function getPromptTemplate(): string
    {
        return self::PROMPT_TEMPLATE;
    }
}
