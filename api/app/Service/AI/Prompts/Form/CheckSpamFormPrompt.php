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
        Analyze this form for spam/phishing. Be CONSERVATIVE - only block obvious threats.

        <formContent>
        {formContent}
        </formContent>

        <userInformation>
        User registered: {userRegisteredSince} days ago.
        User is subscribed: {isSubscribed}
        Total forms created: {totalFormsCreated}
        </userInformation>

        **BLOCK IMMEDIATELY (is_spam: true):**
        - Login forms impersonating major services (Gmail, PayPal, Facebook, Outlook, etc.)
        - Forms asking for existing passwords/credentials to "verify" accounts or for fake login
        - Clear brand impersonation with sensitive data collection

        **FLAG FOR ADMIN REVIEW (needs_admin_review: true):**
        - New users with vague titles but no clear phishing intent
        - Basic info collection (email/phone) without passwords
        - Borderline suspicious but possibly legitimate testing

        **ALLOW (both false):**
        - New account creation forms (password setup normal)
        - Surveys, event codes, referral forms
        - General contact/support forms

        **KEY RULES:**
        - Login forms = phishing, Registration forms = often legitimate
        - Subscribed users get benefit of doubt
        - When uncertain, prefer admin review over blocking

        Classify as: SPAM (block), ADMIN REVIEW (flag), or LEGITIMATE (allow).
        Respond with JSON: {"is_spam": boolean, "needs_admin_review": boolean, "reason": "explanation"}
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
            'needs_admin_review' => [
                'type' => 'boolean',
                'description' => 'True if the form is borderline suspicious and needs admin review but should not be automatically blocked. Only set when is_spam is false.'
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

        // Only include blocking information if user has history
        $hasBlockingHistory = !empty($this->form->creator->meta['blocking_history'] ?? []);

        if ($hasBlockingHistory) {
            $currentBlockStatus = $this->form->creator->is_blocked ? 'BLOCKED' : 'ACTIVE';
            $blockingHistory = $this->extractBlockingHistory();

            $blockingHistorySection = "Current block status: {$currentBlockStatus}\nBlocking history: {$blockingHistory}";

            $blockingConsiderations = "**BLOCKING HISTORY CONSIDERATIONS:**
- If user was previously blocked but manually unblocked by admin, use EXTRA CAUTION before re-blocking
- Manual unblocks indicate admin review and approval - respect this decision
- Only re-block previously reviewed users for CLEAR violations (login forms, obvious phishing)
- Consider the pattern: repeated blocks may indicate persistent bad behavior
- Give benefit of doubt to users who were unblocked and haven't violated since

";

            $adminReviewFactor = "5. **Admin Review History**: If user was manually unblocked, they were likely reviewed and cleared";

            $enhancedTemplate = Str::of($template)
                ->replace('<userInformation>', "<userInformation>\n{$blockingHistorySection}")
                ->replace('CRITICAL ANALYSIS GUIDELINES:', $blockingConsiderations . 'CRITICAL ANALYSIS GUIDELINES:')
                ->replace('4. **User Trust**: Subscribed users and users with multiple forms get more benefit of doubt', '4. **User Trust**: Subscribed users and users with multiple forms get more benefit of doubt' . "\n        {$adminReviewFactor}")
                ->toString();

            return Str::of($enhancedTemplate)
                ->replace('{formContent}', $formContent)
                ->replace('{userRegisteredSince}', $userRegisteredSince)
                ->replace('{isSubscribed}', $isSubscribed)
                ->replace('{totalFormsCreated}', $totalFormsCreated)
                ->toString();
        }

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

    private function extractBlockingHistory(): string
    {
        $history = $this->form->creator->meta['blocking_history'] ?? [];

        if (empty($history)) {
            return "No previous blocking history - clean record.";
        }

        $totalBlocks = count($history);
        $manualUnblocks = collect($history)->filter(fn ($block) => !is_null($block['unblocked_by']))->count();

        $summary = [];
        $summary[] = "Total times blocked: {$totalBlocks}";
        $summary[] = "Manual unblocks by admin: {$manualUnblocks}";

        // Get the most recent block details
        $lastBlock = end($history);
        if ($lastBlock) {
            $summary[] = "Last block reason: " . ($lastBlock['reason'] ?? 'N/A');

            if ($lastBlock['unblocked_by']) {
                $summary[] = "Last unblock reason: " . ($lastBlock['unblock_reason'] ?? 'N/A');
                $summary[] = "STATUS: Previously blocked user was manually reviewed and unblocked by admin.";
            }
        }

        return implode("\n", $summary);
    }
}
