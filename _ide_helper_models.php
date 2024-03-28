<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models\Billing{
/**
 * App\Models\Billing\Subscription
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $stripe_id
 * @property string $stripe_status
 * @property string|null $stripe_price
 * @property int|null $quantity
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Cashier\SubscriptionItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User|null $owner
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription active()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription canceled()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription cancelled()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription ended()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription expiredTrial()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription incomplete()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription notCanceled()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription notCancelled()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription notOnGracePeriod()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription notOnTrial()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription onGracePeriod()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription onTrial()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription pastDue()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription recurring()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStripePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStripeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUserId($value)
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models\Forms\AI{
/**
 * App\Models\Forms\AI\AiFormCompletion
 *
 * @property int $id
 * @property string $form_prompt
 * @property string $status
 * @property mixed|null $result
 * @property string $ip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AiFormCompletion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AiFormCompletion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AiFormCompletion query()
 * @method static \Illuminate\Database\Eloquent\Builder|AiFormCompletion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiFormCompletion whereFormPrompt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiFormCompletion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiFormCompletion whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiFormCompletion whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiFormCompletion whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AiFormCompletion whereUpdatedAt($value)
 */
	class AiFormCompletion extends \Eloquent {}
}

namespace App\Models\Forms{
/**
 * App\Models\Forms\Form
 *
 * @property int $id
 * @property int $workspace_id
 * @property string $title
 * @property string $slug
 * @property array $properties
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $notifies
 * @property string|null $description
 * @property string $submit_button_text
 * @property bool $re_fillable
 * @property string $re_fill_button_text
 * @property string $color
 * @property bool $uppercase_labels
 * @property bool $no_branding
 * @property bool $hide_title
 * @property string $submitted_text
 * @property string $dark_mode
 * @property string|null $webhook_url
 * @property bool $send_submission_confirmation
 * @property string|null $logo_picture
 * @property string|null $cover_picture
 * @property string|null $redirect_url
 * @property string|null $custom_code
 * @property string|null $notification_emails
 * @property string $theme
 * @property array|null $database_fields_update
 * @property string $width
 * @property bool $transparent_background
 * @property \Illuminate\Support\Carbon|null $closes_at
 * @property string|null $closed_text
 * @property string $notification_subject
 * @property string $notification_body
 * @property bool $notifications_include_submission
 * @property bool $use_captcha
 * @property bool $can_be_indexed
 * @property string|null $password
 * @property string $notification_sender
 * @property array|null $tags
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $creator_id
 * @property-read array|null $removed_properties
 * @property int|null $max_submissions_count
 * @property string|null $max_submissions_reached_text
 * @property string|null $slack_webhook_url
 * @property string $visibility
 * @property bool $editable_submissions
 * @property string|null $discord_webhook_url
 * @property string $editable_submissions_button_text
 * @property bool $confetti_on_submission
 * @property object $seo_meta
 * @property object|null $notification_settings
 * @property bool $auto_save
 * @property string|null $custom_domain
 * @property bool $show_progress_bar
 * @property-read \App\Models\User $creator
 * @property-read mixed $edit_url
 * @property-read mixed $form_pending_submission_key
 * @property-read mixed $has_password
 * @property-read mixed $is_closed
 * @property-read mixed $is_pro
 * @property-read mixed $max_file_size
 * @property-read mixed $max_number_of_submissions_reached
 * @property-read mixed $notifies_discord
 * @property-read mixed $notifies_slack
 * @property-read mixed $notifies_webhook
 * @property-read mixed $share_url
 * @property-read int|null $submissions_count
 * @property-read int|null $views_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forms\FormStatistic> $statistics
 * @property-read int|null $statistics_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forms\FormSubmission> $submissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forms\FormView> $views
 * @property-read \App\Models\Workspace|null $workspace
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Integration\FormZapierWebhook> $zappierHooks
 * @property-read int|null $zappier_hooks_count
 * @method static \Database\Factories\FormFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Form newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Form newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Form onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Form query()
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereAutoSave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereCanBeIndexed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereClosedText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereClosesAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereConfettiOnSubmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereCoverPicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereCustomCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereCustomDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereDarkMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereDatabaseFieldsUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereDiscordWebhookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereEditableSubmissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereEditableSubmissionsButtonText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereHideTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereLogoPicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereMaxSubmissionsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereMaxSubmissionsReachedText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereNoBranding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereNotificationBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereNotificationEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereNotificationSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereNotificationSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereNotificationSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereNotificationsIncludeSubmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereNotifies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereReFillButtonText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereReFillable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereRedirectUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereRemovedProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereSendSubmissionConfirmation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereSeoMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereShowProgressBar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereSlackWebhookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereSubmitButtonText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereSubmittedText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereTransparentBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereUppercaseLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereUseCaptcha($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereWebhookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereWorkspaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Form withoutTrashed()
 */
	class Form extends \Eloquent implements \App\Models\Traits\CachableAttributes {}
}

namespace App\Models\Forms{
/**
 * App\Models\Forms\FormStatistic
 *
 * @property int $id
 * @property int $form_id
 * @property array $data
 * @property string $date
 * @property-read \App\Models\Forms\Form|null $form
 * @method static \Illuminate\Database\Eloquent\Builder|FormStatistic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormStatistic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormStatistic query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormStatistic whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormStatistic whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormStatistic whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormStatistic whereId($value)
 */
	class FormStatistic extends \Eloquent {}
}

namespace App\Models\Forms{
/**
 * App\Models\Forms\FormSubmission
 *
 * @property int $id
 * @property int $form_id
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Forms\Form|null $form
 * @method static \Illuminate\Database\Eloquent\Builder|FormSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormSubmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormSubmission whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormSubmission whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormSubmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormSubmission whereUpdatedAt($value)
 */
	class FormSubmission extends \Eloquent {}
}

namespace App\Models\Forms{
/**
 * App\Models\Forms\FormView
 *
 * @property int $id
 * @property int $form_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Forms\Form|null $form
 * @method static \Illuminate\Database\Eloquent\Builder|FormView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormView query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormView whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormView whereUpdatedAt($value)
 */
	class FormView extends \Eloquent {}
}

namespace App\Models\Integration{
/**
 * App\Models\Integration\FormIntegration
 *
 * @property int $id
 * @property int $form_id
 * @property string $status
 * @property string $integration_id
 * @property object $logic
 * @property object $data
 * @property string|null $oauth_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Integration\FormIntegrationsEvent> $events
 * @property-read int|null $events_count
 * @property-read \App\Models\Forms\Form|null $form
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegration query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegration whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegration whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegration whereIntegrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegration whereLogic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegration whereOauthId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegration whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegration whereUpdatedAt($value)
 */
	class FormIntegration extends \Eloquent {}
}

namespace App\Models\Integration{
/**
 * App\Models\Integration\FormIntegrationsEvent
 *
 * @property int $id
 * @property int $integration_id
 * @property string $status
 * @property object $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Integration\FormIntegration|null $integration
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegrationsEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegrationsEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegrationsEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegrationsEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegrationsEvent whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegrationsEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegrationsEvent whereIntegrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegrationsEvent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormIntegrationsEvent whereUpdatedAt($value)
 */
	class FormIntegrationsEvent extends \Eloquent {}
}

namespace App\Models\Integration{
/**
 * App\Models\Integration\FormZapierWebhook
 *
 * @property int $id
 * @property int $form_id
 * @property string $hook_url
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Forms\Form|null $form
 * @method static \Illuminate\Database\Eloquent\Builder|FormZapierWebhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormZapierWebhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormZapierWebhook onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormZapierWebhook query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormZapierWebhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormZapierWebhook whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormZapierWebhook whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormZapierWebhook whereHookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormZapierWebhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormZapierWebhook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormZapierWebhook withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormZapierWebhook withoutTrashed()
 */
	class FormZapierWebhook extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\License
 *
 * @property int $id
 * @property string $license_key
 * @property int|null $user_id
 * @property string $license_provider
 * @property string $status
 * @property array $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int|null $custom_domain_limit_count
 * @property-read int $max_file_size
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|License active()
 * @method static \Illuminate\Database\Eloquent\Builder|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License query()
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereLicenseKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereLicenseProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereUserId($value)
 */
	class License extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OAuthProvider
 *
 * @property int $id
 * @property int $user_id
 * @property string $provider
 * @property string $provider_user_id
 * @property string|null $access_token
 * @property string|null $refresh_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthProvider whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthProvider whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthProvider whereProviderUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthProvider whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthProvider whereUserId($value)
 */
	class OAuthProvider extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Template
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string|null $image_url
 * @property array $structure
 * @property array $questions
 * @property bool $publicly_listed
 * @property array $industries
 * @property array $types
 * @property string|null $short_description
 * @property array $related_templates
 * @property int|null $creator_id
 * @property-read mixed $share_url
 * @method static \Illuminate\Database\Eloquent\Builder|Template newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Template newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Template publiclyListed()
 * @method static \Illuminate\Database\Eloquent\Builder|Template query()
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereIndustries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template wherePubliclyListed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereRelatedTemplates($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereStructure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereUpdatedAt($value)
 */
	class Template extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $trial_ends_at
 * @property string|null $hear_about_us
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Template> $formTemplates
 * @property-read int|null $form_templates_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forms\Form> $forms
 * @property-read int|null $forms_count
 * @property-read mixed $admin
 * @property-read mixed $has_customer_id
 * @property-read mixed $has_forms
 * @property-read mixed $is_risky
 * @property-read mixed $is_subscribed
 * @property-read mixed $moderator
 * @property-read string $photo_url
 * @property-read mixed $template_editor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\License> $licenses
 * @property-read int|null $licenses_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OAuthProvider> $oauthProviders
 * @property-read int|null $oauth_providers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Billing\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Workspace> $workspaces
 * @property-read int|null $workspaces_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHearAboutUs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePmLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePmType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withActiveSubscription()
 */
	class User extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject {}
}

namespace App\Models{
/**
 * App\Models\Workspace
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $icon
 * @property array|null $custom_domains
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forms\Form> $forms
 * @property-read int|null $forms_count
 * @property-read mixed $custom_domain_count_limit
 * @property-read mixed $is_enterprise
 * @property-read mixed $is_pro
 * @property-read mixed $is_risky
 * @property-read mixed $max_file_size
 * @property-read mixed $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Workspace newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Workspace newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Workspace query()
 * @method static \Illuminate\Database\Eloquent\Builder|Workspace whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workspace whereCustomDomains($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workspace whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workspace whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workspace whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workspace whereUpdatedAt($value)
 */
	class Workspace extends \Eloquent implements \App\Models\Traits\CachableAttributes {}
}

