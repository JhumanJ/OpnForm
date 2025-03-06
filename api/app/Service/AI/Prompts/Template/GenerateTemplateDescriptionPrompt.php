<?php

namespace App\Service\AI\Prompts\Template;

use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class GenerateTemplateDescriptionPrompt extends Prompt
{
    protected float $temperature = 0.81;

    protected int $maxTokens = 2000;

    /**
     * The prompt template for generating form descriptions
     */
    public const PROMPT_TEMPLATE = <<<'EOD'
        Create detailed HTML content for a form template page about: "{templatePrompt}".
        
        The HTML content should have the following structure:
        1. A paragraph explaining what the template is about
        2. A section with heading (h2) explaining why and when to use such a form
        3. A section with heading (h2) explaining who the target audience is and why it's beneficial to build this form
        4. A section with heading (h2) explaining that OpnForm is the best tool to build this form, highlighting:
           - Easy duplication of this template in seconds
           - Integration capabilities with other tools through webhooks or Zapier
           - Open-source and free to use nature
        
        Use only h2, p, ul, and li HTML tags. Each section (except for the first one) MUST start with an h2 tag containing a title.
    EOD;

    public function __construct(
        public string $templatePrompt
    ) {
        parent::__construct();
    }

    protected function getSystemMessage(): ?string
    {
        return 'You are an assistant helping to generate comprehensive form descriptions. Create well-structured, informative HTML content that explains the purpose, benefits, and target audience of the form template.';
    }

    protected function getPromptTemplate(): string
    {
        return self::PROMPT_TEMPLATE;
    }
}
