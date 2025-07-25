<?php

namespace App\Service\AI\Prompts\Form;

use App\Models\Forms\Form;
use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class CheckSpamFormPrompt extends Prompt
{
    protected ?float $temperature = 0.2;
    protected ?int $maxTokens = 500;
    protected string $model = 'gpt-4.1-mini';

    public const PROMPT_TEMPLATE = <<<'EOD'
        You are an AI assistant specialized in detecting spam, phishing, and fraudulent content in online forms.
        Analyze the following form content and determine if it violates our policies.

        <formContent>
        {formContent}
        </formContent>

        <userInformation>
        User registered: {userRegisteredSince} days ago.
        User is subscribed: {isSubscribed}
        Total forms created by user: {totalFormsCreated}
        </userInformation>

        CRITICAL ANALYSIS GUIDELINES:

        **IMMEDIATE SPAM/PHISHING (Always Block):**
        - ANY login forms claiming to be from major services (Gmail, Outlook, Yahoo, PayPal, Facebook, Microsoft, Google, Apple, etc.)
        - Forms explicitly impersonating company authentication flows
        - Any form asking for existing passwords or login credentials to "verify" accounts
        - Forms with company branding/logos claiming to be official login pages

        **SUSPICIOUS PATTERNS (High Scrutiny):**
        - New users (<7 days) creating forms with sensitive data collection
        - Forms asking for passwords in combination with brand names
        - Short forms (1-3 fields) requesting only sensitive information
        - Forms collecting credit card, SSN, or banking details without clear business purpose

        **LEGITIMATE CASES (Generally Allowed):**
        - Account creation/registration forms for legitimate services (password setup is normal)
        - Employee onboarding forms requiring password creation
        - Survey forms asking about security practices (research purposes)
        - Event codes, conference codes, referral codes, discount codes
        - Customer support forms for account assistance
        - Secret codes for contests or events (NOT for account access)

        **KEY DECISION FACTORS:**
        1. **Login vs Registration**: Login forms are almost always phishing. Registration/signup forms mayb be legitimate.
        2. **Brand Impersonation**: Any clear impersonation of major companies is prohibited
        3. **User Trust**: Subscribed users and users with multiple forms get more benefit of doubt
        4. **Form Context**: Consider total fields, form purpose, and data types being collected

        **PASSWORD/SECRET GUIDELINES:**
        - Password fields are acceptable for NEW account creation
        - Password fields are NOT acceptable for login to existing accounts as it's not a login form, just a data collection form
        - "Secret codes" are acceptable for events/contests but NOT for account recovery
        - Never allow forms asking for existing passwords/credentials/PIN

        Based on this analysis, classify the form as spam or legitimate.
        
        Respond with a valid JSON object with your analysis.
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
        $isSubscribed = $this->form->creator->is_subscribed ? 'yes' : 'no';
        $totalFormsCreated = $this->form->creator->forms()->count();

        return Str::of($template)
            ->replace('{formContent}', $formContent)
            ->replace('{userRegisteredSince}', $userRegisteredSince)
            ->replace('{isSubscribed}', $isSubscribed)
            ->replace('{totalFormsCreated}', $totalFormsCreated)
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
