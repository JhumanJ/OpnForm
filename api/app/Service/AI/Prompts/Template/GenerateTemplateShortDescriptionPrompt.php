<?php

namespace App\Service\AI\Prompts\Template;

use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class GenerateTemplateShortDescriptionPrompt extends Prompt
{
    protected float $temperature = 0.81;

    protected int $maxTokens = 500;

    /**
     * The prompt template for generating short form descriptions
     */
    public const PROMPT_TEMPLATE = <<<'EOD'
        Create a single-sentence description for a form template about: "{templatePrompt}".
        
        The description should:
        - Be concise and to the point (one sentence only)
        - Clearly explain what the form is about
        - Highlight the main purpose or benefit of the form
        - Be suitable for OpnForm, a free-to-use form builder
        
        Do not use quotation marks in your response.
    EOD;

    public function __construct(
        public string $templatePrompt
    ) {
        parent::__construct();
    }

    protected function getSystemMessage(): ?string
    {
        return 'You are an assistant helping to generate short form descriptions. Create concise, informative single-sentence descriptions that clearly communicate the purpose of form templates.';
    }

    protected function getPromptTemplate(): string
    {
        return self::PROMPT_TEMPLATE;
    }

    /**
     * Override the execute method to automatically process the output
     */
    public function execute(): string
    {
        $this->initialize();
        $prompt = $this->buildPrompt();

        $this->completer->doesNotExpectJson();
        $this->completer->completeChat(
            [['role' => 'user', 'content' => $prompt]],
            $this->maxTokens,
            $this->temperature
        );

        $description = $this->completer->getString();
        return $this->processOutput($description);
    }

    /**
     * Process the output to remove quotes if present
     */
    public function processOutput(string $description): string
    {
        // Remove quotes if the description is enclosed in them
        return preg_replace('/^["\'](.*)["\']$/', '$1', $description);
    }
}
