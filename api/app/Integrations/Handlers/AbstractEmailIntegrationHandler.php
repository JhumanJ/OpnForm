<?php

namespace App\Integrations\Handlers;

use App\Events\Forms\FormSubmitted;
use App\Models\Integration\FormIntegration;
use Illuminate\Support\Facades\Log;

abstract class AbstractEmailIntegrationHandler extends AbstractIntegrationHandler
{
    protected $mailer;

    public function __construct(FormSubmitted $event, FormIntegration $formIntegration, array $integration)
    {
        parent::__construct($event, $formIntegration, $integration);
        $this->initializeMailer();
    }

    protected function initializeMailer()
    {
        $this->mailer = config('mail.default');
        $this->setWorkspaceSMTPSettings();

        if (!$this->mailer) {
            Log::error('Mailer not specified', [
                'form_id' => $this->form->id
            ]);
        }
    }

    protected function setWorkspaceSMTPSettings()
    {
        $workspace = $this->form->workspace;
        $emailSettings = $workspace->settings['email_settings'] ?? [];
        if (!$workspace->is_pro || !$emailSettings || empty($emailSettings['host']) || empty($emailSettings['port']) || empty($emailSettings['username']) || empty($emailSettings['password'])) {
            return;
        }

        config([
            'mail.mailers.custom_smtp.host' => $emailSettings['host'],
            'mail.mailers.custom_smtp.port' => $emailSettings['port'],
            'mail.mailers.custom_smtp.username' => $emailSettings['username'],
            'mail.mailers.custom_smtp.password' => $emailSettings['password']
        ]);
        $this->mailer = 'custom_smtp';
    }
}
