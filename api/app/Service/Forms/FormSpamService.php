<?php

namespace App\Service\Forms;

use App\Models\Forms\Form;
use App\Models\User;
use App\Service\AI\Prompts\Form\CheckSpamFormPrompt;
use App\Service\UserActionService;
use Illuminate\Support\Facades\Log;

class FormSpamService
{
    public function __construct(protected UserActionService $userActionService)
    {
    }

    public function checkForm(Form $form): void
    {
        if (!config('spam.enabled') || !$this->shouldCheck($form)) {
            return;
        }

        try {
            $result = CheckSpamFormPrompt::run($form);

            if ($result['is_spam']) {
                $reason = 'AI detected potential policy violation: ' . $result['reason'];
                $this->userActionService->block($form->creator, $reason, null);
            }
        } catch (\Exception $e) {
            Log::error('Failed to check form for spam.', [
                'form_id' => $form->id,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    private function shouldCheck(Form $form): bool
    {
        if ($form->creator->is_blocked || $form->creator->admin || $form->creator->moderator) {
            return false;
        }

        if ($this->containsKeywords($form)) {
            return true;
        }

        if ($this->isRiskyUser($form->creator)) {
            return true;
        }

        // Random check for other users
        return (rand(1, 100) <= config('spam.random_check_percentage', 0));
    }

    private function isRiskyUser(User $user): bool
    {
        // Example: Free user registered in the last N days
        return !$user->is_subscribed && $user->created_at->diffInDays(now()) <= config('spam.risky_user_days', 7);
    }

    private function containsKeywords(Form $form): bool
    {
        $content = json_encode($form->fields) . ' ' . $form->title . ' ' . $form->description;
        $content = strtolower($content);
        $keywords = config('spam.keywords', []);

        foreach ($keywords as $keyword) {
            if (str_contains($content, strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }
}
