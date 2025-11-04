<?php

namespace App\Service\AI\Prompts\Integration;

use App\Models\Forms\Form;
use App\Models\Integration\FormIntegration;
use App\Service\AI\Prompts\Prompt;
use Illuminate\Support\Str;

class CheckSpamEmailIntegrationPrompt extends Prompt
{
    protected ?float $temperature = 0.2;
    protected ?int $maxTokens = 500;
    protected string $model = 'gpt-4.1-mini';

    public const PROMPT_TEMPLATE = <<<'EOD'
        Analyze this email notification integration for spam/phishing or unrelated content.

        <emailIntegration>
        Subject: {subject}
        Body:
        {emailContent}
        Includes submission data: {includesSubmissionData}
        Reply-to: {replyTo}
        Link edit submission: {linkEditSubmission}
        </emailIntegration>

        <formContext>
        Form title: {formTitle}
        Form description: {formDescription}
        </formContext>

        <userInformation>
        User registered: {userRegisteredSince} days ago.
        User is subscribed: {isSubscribed}
        </userInformation>

        Guidelines:
        - BLOCK immediately if email content is phishing, brand impersonation, login credential harvesting, or clearly unrelated to the form context.
        - FLAG FOR ADMIN REVIEW only when borderline suspicious, but not clearly malicious.
        - ALLOW by default for normal notifications about the form or its submissions.

        Respond with JSON: {"is_spam": boolean, "needs_admin_review": boolean, "reason": "brief explanation"}
    EOD;

    protected ?array $jsonSchema = [
        'type' => 'object',
        'required' => ['is_spam', 'needs_admin_review', 'reason'],
        'additionalProperties' => false,
        'properties' => [
            'is_spam' => [
                'type' => 'boolean',
            ],
            'needs_admin_review' => [
                'type' => 'boolean',
            ],
            'reason' => [
                'type' => 'string',
            ],
        ],
    ];

    public function __construct(
        public Form $form,
        public FormIntegration $integration
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
        $data = (array) ($this->integration->data ?? (object) []);

        $subject = (string) ($data['subject'] ?? '');
        $emailContent = (string) ($data['email_content'] ?? '');
        $includesSubmissionData = isset($data['include_submission_data']) && $data['include_submission_data'] ? 'yes' : 'no';
        $replyTo = (string) ($data['reply_to'] ?? '');
        $linkEditSubmission = isset($data['link_edit_submission']) && $data['link_edit_submission'] ? 'yes' : 'no';

        $userRegisteredSince = $this->form->creator->created_at->diffInDays(now());
        $isSubscribed = $this->form->creator->is_subscribed ? 'yes' : 'no';

        return Str::of($template)
            ->replace('{subject}', $subject)
            ->replace('{emailContent}', $emailContent)
            ->replace('{includesSubmissionData}', $includesSubmissionData)
            ->replace('{replyTo}', $replyTo)
            ->replace('{linkEditSubmission}', $linkEditSubmission)
            ->replace('{formTitle}', (string) $this->form->title)
            ->replace('{formDescription}', (string) $this->form->description)
            ->replace('{userRegisteredSince}', (string) $userRegisteredSince)
            ->replace('{isSubscribed}', $isSubscribed)
            ->toString();
    }
}
