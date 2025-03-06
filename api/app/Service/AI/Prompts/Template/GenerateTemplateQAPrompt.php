<?php

namespace App\Service\AI\Prompts\Template;

use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class GenerateTemplateQAPrompt extends Prompt
{
    protected float $temperature = 0.7;

    protected int $maxTokens = 1000;

    /**
     * The prompt template for generating Q&A content
     */
    public const PROMPT_TEMPLATE = <<<'EOD'
        I'm creating a form template with the following description:
        
        "{formDescription}"
        
        Based on this description, create 4 to 6 question and answer pairs for the template page.
        
        The questions should cover:
        - The purpose and benefits of this form template
        - When and why to use this form
        - Who the target audience is
        - Why OpnForm is the best option to create this form (open-source, free to use, integrations, etc.)
        
        Reply only with a valid JSON array of objects, each containing "question" and "answer" keys, like this:
        ```json
        [
          {
            "question": "What is this form template for?",
            "answer": "This form template is designed to..."
          },
          {
            "question": "Who should use this form?",
            "answer": "This form is ideal for..."
          }
        ]
        ```
    EOD;

    /**
     * JSON schema for Q&A output
     */
    protected ?array $jsonSchema = [
        'type' => 'array',
        'items' => [
            'type' => 'object',
            'required' => ['question', 'answer'],
            'properties' => [
                'question' => [
                    'type' => 'string',
                    'description' => 'The question about the form template'
                ],
                'answer' => [
                    'type' => 'string',
                    'description' => 'The answer to the question'
                ]
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
        return 'You are an assistant helping to generate Q&A content for form templates. Create informative questions and answers that highlight the benefits of the form and OpnForm platform.';
    }

    protected function getPromptTemplate(): string
    {
        return self::PROMPT_TEMPLATE;
    }
}
