<?php


namespace App\Http\Requests;


use App\Models\Forms\Form;
use App\Rules\OneEmailPerLine;
use Illuminate\Validation\Rule;
use App\Rules\FormPropertyLogicRule;

/**
 * Abstract class to validate create/update forms
 *
 * Class UserFormRequest
 * @package App\Http\Requests
 */
abstract class UserFormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Form Info
            'title' => 'required|string|max:60',
            'description' => 'nullable|string|max:2000',
            'tags' => 'nullable|array',
            'visibility' => ['required',Rule::in(Form::VISIBILITY)],

            // Notifications
            'notifies' => 'boolean',
            'notification_emails' => ['required_if:notifies,1', new OneEmailPerLine ],
            'send_submission_confirmation' => 'boolean',
            'notification_sender' => 'string|max:64',
            'notification_subject' => 'string|max:200',
            'notification_body' => 'string|nullable',
            'notifications_include_submission' => 'boolean',
            'webhook_url' => 'url|nullable',
            'use_captcha' => 'boolean',
            'slack_webhook_url' => 'url|nullable',

            // Customization
            'theme' => ['required',Rule::in(Form::THEMES)],
            'width' => ['required',Rule::in(Form::WIDTHS)],
            'cover_picture' => 'url|nullable',
            'logo_picture' => 'url|nullable',
            'dark_mode' => ['required',Rule::in(Form::DARK_MODE_VALUES)],
            'color' => 'required|string',
            'hide_title' => 'required|boolean',
            'uppercase_labels' => 'required|boolean',
            'no_branding' => 'required|boolean',
            'transparent_background' => 'required|boolean',
            'closes_at' => 'date|nullable',
            'closed_text' => 'string|nullable',

            // Custom Code
            'custom_code' => 'string|nullable',

            // Submission
            'submit_button_text' => 'string|min:1|max:50',
            're_fillable' => 'boolean',
            're_fill_button_text' => 'string|min:1|max:50',
            'submitted_text' => 'string|max:2000',
            'redirect_url' => 'nullable|active_url|max:255',
            'database_fields_update' => 'nullable|array',
            'max_submissions_count' => 'integer|nullable|min:1',
            'max_submissions_reached_text' => 'string|nullable',
            'editable_submissions' => 'boolean|nullable',

            // Properties
            'properties' => 'required|array',
            'properties.*.id' => 'required',
            'properties.*.name' => 'required',
            'properties.*.type' => 'required',
            'properties.*.placeholder' => 'sometimes|nullable',
            'properties.*.prefill' => 'sometimes|nullable',
            'properties.*.help' => 'sometimes|nullable',
            'properties.*.hidden' => 'boolean|nullable',
            'properties.*.required' => 'boolean|nullable',
            'properties.*.multiple' => 'boolean|nullable',
            'properties.*.timezone' => 'sometimes|nullable',
            'properties.*.width' => ['sometimes', Rule::in(['full','1/2','1/3','2/3','1/3','3/4','1/4'])],
            'properties.*.allowed_file_types' => 'sometimes|nullable',
            'properties.*.use_toggle_switch' => 'boolean|nullable',

            // Logic
            'properties.*.logic' => ['array', 'nullable', new FormPropertyLogicRule()],

            // Form blocks
            'properties.*.content' => 'sometimes|nullable',

            // Text field
            'properties.*.multi_lines' => 'boolean|nullable',
            'properties.*.max_char_limit' => 'integer|nullable|min:1|max:2000',
            'properties.*.show_char_limit ' => 'boolean|nullable',

            // Date field
            'properties.*.with_time' => 'boolean|nullable',
            'properties.*.date_range' => 'boolean|nullable',
            'properties.*.prefill_today' => 'boolean|nullable',
            'properties.*.disable_past_dates' => 'boolean|nullable',
            'properties.*.disable_future_dates' => 'boolean|nullable',

            // Select / Multi Select field
            'properties.*.allow_creation' => 'boolean|nullable',
            'properties.*.without_dropdown' => 'boolean|nullable',

            // Advanced Options
            'properties.*.generates_uuid' => 'boolean|nullable',
            'properties.*.generates_auto_increment_id' => 'boolean|nullable',

            // Security & Privacy
            'can_be_indexed' => 'boolean',
            'password' => 'sometimes|nullable',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'properties.*.name.required' => 'The form block number :position is missing a name.',
            'properties.*.type.required' => 'The form block number :position is missing a type.',
            'properties.*.max_char_limit.min' => 'The form block number :position max character limit must be at least 1 OR Empty',
            'properties.*.max_char_limit.max' => 'The form block number :position max character limit may not be greater than 2000.',
        ];
    }
}
