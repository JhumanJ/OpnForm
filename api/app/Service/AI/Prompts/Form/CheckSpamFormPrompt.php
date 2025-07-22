<?php

namespace App\Service\AI\Prompts\Form;

use App\Models\Forms\Form;
use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class CheckSpamFormPrompt extends Prompt
{
    protected ?float $temperature = 0.2;
    protected ?int $maxTokens = 500;
    protected string $model = 'gpt-4.1';

    public const PROMPT_TEMPLATE = <<<'EOD'
        You are an AI assistant specialized in detecting spam, phishing, and fraudulent content in online forms.
        Analyze the following form content and determine if it violates our policies.

        Form Content:
        ---
        {formContent}
        ---

        User Information:
        - User registered: {userRegisteredSince} days ago.

        Based on the content and user data, classify the form. The form is considered spam/scam if it attempts to collect sensitive information like passwords, credit card details, social security numbers, or impersonates well-known brands for phishing purposes.

        Please respond with a valid JSON object with your analysis.
    EOD;

    protected ?array $jsonSchema = [
        'type' => 'object',
        'required' => ['is_spam', 'reason'],
        'additionalProperties' => false,
        'properties' => [
            'is_spam' => [
                'type' => 'boolean',
                'description' => 'True if the form is considered spam/scam, false otherwise.'
            ],
            'reason' => [
                'type' => 'string',
                'description' => 'A brief explanation for your decision.'
            ]
        ]
    ];

    public function __construct(
        public Form $form
    ) {
        parent::__construct();
    }

    protected function getPromptTemplate(): string
    {
        return self::PROMPT_TEMPLATE;
    }

    protected function buildPrompt(): string
    {
        $template = $this->getPromptTemplate();
        $formContent = $this->extractFormContent();
        $userRegisteredSince = $this->form->creator->created_at->diffInDays(now());

        return Str::of($template)
            ->replace('{formContent}', $formContent)
            ->replace('{userRegisteredSince}', $userRegisteredSince)
            ->toString();
    }

    private function extractFormContent(): string
    {
        $content = [];
        $content[] = "Title: " . $this->form->title;
        $content[] = "Description: " . $this->form->description;

        foreach ($this->form->properties as $field) {
            $content[] = "- Field Name: " . ($field['name'] ?? 'N/A');
            $content[] = "  Field Type: " . ($field['type'] ?? 'N/A');
            $content[] = "  Placeholder: " . ($field['placeholder'] ?? 'N/A');
        }

        return implode("\n", $content);
    }
}
