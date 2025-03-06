<?php

namespace App\Service\AI\Prompts\Template;

use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class GenerateTemplateIndustryPrompt extends Prompt
{
    protected float $temperature = 0.7;

    protected int $maxTokens = 500;

    /**
     * The prompt template for generating industry classifications
     */
    public const PROMPT_TEMPLATE = <<<'EOD'
        I need to classify a form template into appropriate industries. The template is about: "{templatePrompt}"
        
        You must assign the template to industries. Return a list of industries (minimum 1, maximum 3 but only if very relevant) and order them by relevance (most relevant first).
        
        Here are the only industries you can choose from: [INDUSTRIES]
        
        DO NOT create new industries. Only select from the provided list.
        
        Reply with a valid JSON array of industry slugs, like this:
        ```json
        ["industry-slug-1", "industry-slug-2"]
        ```
    EOD;

    /**
     * JSON schema for industry output
     */
    protected ?array $jsonSchema = [
        'type' => 'array',
        'items' => [
            'type' => 'string',
            'description' => 'Industry slug'
        ]
    ];

    public function __construct(
        public string $templatePrompt,
        public array $availableIndustries
    ) {
        parent::__construct();
    }

    protected function getSystemMessage(): ?string
    {
        return 'You are an assistant helping to classify form templates into appropriate industries. Select only from the provided list of industries, choosing the most relevant ones for the template.';
    }

    protected function getPromptTemplate(): string
    {
        return self::PROMPT_TEMPLATE;
    }

    protected function buildPrompt(): string
    {
        $industriesString = implode(', ', $this->availableIndustries);

        return Str::of($this->getPromptTemplate())
            ->replace('[INDUSTRIES]', $industriesString)
            ->toString();
    }
}
