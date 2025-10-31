<?php

namespace App\Service\Integrations;

use App\Models\Forms\Form;
use App\Models\Integration\FormIntegration;
use App\Service\AI\Prompts\Integration\CheckSpamEmailIntegrationPrompt;
use Illuminate\Support\Facades\Log;

class EmailIntegrationSpamService
{
    public function __construct()
    {
    }

    public function checkForSpam(Form $form, FormIntegration $integration): ?array
    {
        if (!$this->shouldCheck($form)) {
            return null;
        }

        try {
            $result = CheckSpamEmailIntegrationPrompt::run($form, $integration);

            if (($result['is_spam'] ?? false) === true) {
                Log::channel('slack_admin')->info('⚠️ Email integration flagged for admin review', [
                    'form_id' => $form->id,
                    'integration_id' => $integration->id,
                    'user_id' => $form->creator->id,
                    'user_email' => $form->creator->email,
                    'reason' => $result['reason'] ?? 'Email integration detected as spam/phishing'
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to check email integration for spam.', [
                'form_id' => $form->id,
                'integration_id' => $integration->id,
                'user_id' => $form->creator->id,
                'user_email' => $form->creator->email,
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function shouldCheck(Form $form): bool
    {
        // Skip if globally disabled
        if (!config('spam.enabled')) {
            return false;
        }

        $user = $form->creator;
        if ($user->is_blocked || $user->admin || $user->moderator) {
            return false;
        }

        return $user->is_risky;
    }
}
