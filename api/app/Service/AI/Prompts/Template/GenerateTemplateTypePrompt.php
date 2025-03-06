<?php

namespace App\Service\AI\Prompts\Template;

use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class GenerateTemplateTypePrompt extends Prompt
{
    protected float $temperature = 0.7;

    protected int $maxTokens = 500;

    /**
     * The prompt template for generating type classifications
     */
    public const PROMPT_TEMPLATE = <<<'EOD'
        I need to classify a form template into appropriate types. The template is about: "{templatePrompt}"
        
        You must assign the template to one or more types. Return a list of types (minimum 1, maximum 3 but only if very accurate) and order them by relevance (most relevant first).
        
        Here are the only types you can choose from: [TYPES]
        
        DO NOT create new types. Only select from the provided list.
        
        Reply with a valid JSON array of type slugs, like this:
        ```json
        ["type-slug-1", "type-slug-2"]
        ```
    EOD;

    /**
     * JSON schema for type output
     */
    protected ?array $jsonSchema = [
        'type' => 'array',
        'items' => [
            'type' => 'string',
            'description' => 'Type slug'
        ]
    ];

    public function __construct(
        public string $templatePrompt,
        public array $availableTypes
    ) {
        parent::__construct();
    }

    protected function getSystemMessage(): ?string
    {
        return 'You are an assistant helping to classify form templates into appropriate types. Select only from the provided list of types, choosing the most relevant ones for the template.';
    }

    protected function getPromptTemplate(): string
    {
        return self::PROMPT_TEMPLATE;
    }

    protected function buildPrompt(): string
    {
        $typesString = implode(', ', $this->availableTypes);

        return Str::of($this->getPromptTemplate())
            ->replace('[TYPES]', $typesString)
            ->toString();
    }
}
