<?php

namespace App\Http\Requests\Templates;

use App\Models\Template;
use Illuminate\Foundation\Http\FormRequest;

class CreateTemplateRequest extends FormRequest
{
    const IGNORED_KEYS = [
        'id',
        'creator',
        'cleanings',
        'closes_at',
        'deleted_at',
        'updated_at',
        'form_pending_submission_key',
        'is_closed',
        'is_pro',
        'is_password_protected',
        'last_edited_human',
        'max_number_of_submissions_reached',
        'notifies',
        'notification_body',
        'notification_emails',
        'notification_sender',
        'notification_subject',
        'notifications_include_submission',
        'notifies_slack',
        'slack_webhook_url',
        'removed_properties',
        'creator_id',
        'extra',
        'workspace',
        'workspace_id',
        'submissions',
        'submissions_count',
        'views',
        'views_count',
        'visibility',
        'webhook_url',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'form' => 'required|array',
            'name' => 'required|string|max:60',
            'slug' => 'required|string|unique:templates',
            'description' => 'required|string|max:2000',
            'image_url' => 'required|string',
            'questions' => 'array',
        ];
    }

    public function getTemplate(): Template
    {
        $structure = $this->form;
        foreach ($structure as $key => $val) {
            if (in_array($key, self::IGNORED_KEYS)) {
                unset($structure[$key]);
            }
        }
        return new Template([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'structure' => $structure,
            'questions' => $this->questions ?? []
        ]);
    }
}
