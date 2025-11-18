<?php

namespace App\Service\Telemetry;

enum TelemetryEvent: string
{
    case INSTANCE_CREATED = 'instance.created';
    case USER_CREATED = 'user.created';
    case FORM_CREATED = 'form.created';
    case WORKSPACE_CREATED = 'workspace.created';
    case FORM_SUBMISSION = 'form.submission';

    public function value(): string
    {
        return $this->value;
    }
}
