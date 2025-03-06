<?php

namespace App\Service\AI\Prompts\Template;

use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class GenerateTemplateTitlePrompt extends Prompt
{
    protected float $temperature = 0.7;

    protected int $maxTokens = 100;

    /**
     * The prompt template for generating template titles
     */
    public const PROMPT_TEMPLATE = <<<'EOD'
        I'm creating a form template with the following description:
        
        "{formDescription}"
        
        Create a title for this form template. The title must:
        - Contain or end with the word "template"
        - Be short and to the point
        - Not include any quotes
        - Be optimized for SEO
        - Clearly describe the purpose of the form
    EOD;

    public function __construct(
        public string $formDescription
    ) {
        parent::__construct();
    }

    protected function getSystemMessage(): ?string
    {
        return 'You are an assistant helping to generate titles for form templates. Create concise, descriptive titles that clearly communicate the form\'s purpose.';
    }

    protected function getPromptTemplate(): string
    {
        return self::PROMPT_TEMPLATE;
    }
}
