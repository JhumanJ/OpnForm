<?php

namespace App\Models\Forms;

use App\Events\Models\FormCreated;
use App\Models\Integration\FormZapierWebhook;
use App\Models\Traits\CachableAttributes;
use App\Models\Traits\CachesAttributes;
use App\Models\User;
use App\Models\Workspace;
use Database\Factories\FormFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Form extends Model implements CachableAttributes
{
    use CachesAttributes;

    const DARK_MODE_VALUES = ['auto', 'light', 'dark'];
    const THEMES = ['default', 'simple', 'notion'];
    const WIDTHS = ['centered', 'full'];
    const VISIBILITY = ['public', 'draft', 'closed'];

    use HasFactory, HasSlug, SoftDeletes;

    protected $fillable = [
        'workspace_id',
        'creator_id',
        'properties',
        'removed_properties',

        // Notifications
        'notifies',
        'notification_emails',
        'send_submission_confirmation',
        'notification_sender',
        'notification_subject',
        'notification_body',
        'notifications_include_submission',
        'slack_webhook_url',
        'discord_webhook_url',
        'notification_settings',

        // integrations
        'webhook_url',

        'title',
        'description',
        'tags',
        'visibility',

        // Customization
        'custom_domain',
        'theme',
        'width',
        'cover_picture',
        'logo_picture',
        'dark_mode',
        'color',
        'uppercase_labels',
        'no_branding',
        'hide_title',
        'transparent_background',

        // Custom Code
        'custom_code',

        // Submission
        'submit_button_text',
        'database_fields_update',
        're_fillable',
        're_fill_button_text',
        'submitted_text',
        'redirect_url',
        'use_captcha',
        'closes_at',
        'closed_text',
        'max_submissions_count',
        'max_submissions_reached_text',
        'editable_submissions',
        'editable_submissions_button_text',
        'confetti_on_submission',
        'auto_save',

        // Security & Privacy
        'can_be_indexed',
        'password',

        // Custom SEO
        'seo_meta'
    ];

    protected $casts = [
        'properties' => 'array',
        'database_fields_update' => 'array',
        'closes_at' => 'datetime',
        'tags' => 'array',
        'removed_properties' => 'array',
        'seo_meta' => 'object',
        'notification_settings' => 'object'
    ];

    protected $appends = [
        'share_url',
    ];

    protected $hidden = [
        'workspace_id',
        'notifies',
        'slack_webhook_url',
        'discord_webhook_url',
        'webhook_url',
        'send_submission_confirmation',
        'redirect_url',
        'database_fields_update',
        'notification_sender',
        'notification_subject',
        'notification_body',
        'notifications_include_submission',
        'password',
        'tags',
        'notification_emails',
        'removed_properties'
    ];

    protected $cachableAttributes = [
        'is_pro',
        'views_count',
        'max_file_size'
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => FormCreated::class,
    ];

    public function getIsProAttribute()
    {
        return $this->remember('is_pro', 15 * 60, function (): ?bool {
            return optional($this->workspace)->is_pro === true;
        });
    }

    public function getShareUrlAttribute()
    {
        if ($this->custom_domain) {
            return 'https://' . $this->custom_domain . '/forms/' . $this->slug;
        }
        return front_url('/forms/' . $this->slug);
    }

    public function getEditUrlAttribute()
    {
        return front_url('/forms/' . $this->slug . '/show');
    }

    public function getSubmissionsCountAttribute()
    {
        return $this->submissions()->count();
    }

    public function getViewsCountAttribute()
    {
        return $this->remember('views_count', 15 * 60, function (): int {
            if (env('DB_CONNECTION') == 'mysql') {
                return (int)($this->views()->count() +
                    $this->statistics()->sum(DB::raw("json_extract(data, '$.views')")));
            }
            return $this->views()->count() +
                $this->statistics()->sum(DB::raw("cast(data->>'views' as integer)"));
        });
    }

    public function setDescriptionAttribute($value)
    {
        // Strip out unwanted html
        $this->attributes['description'] = Purify::clean($value);
    }

    public function setSubmittedTextAttribute($value)
    {
        // Strip out unwanted html
        $this->attributes['submitted_text'] = Purify::clean($value);
    }

    public function setTagsAttribute($value)
    {
        if ($value == '') {
            $value = null;
        }
        $this->attributes['tags'] = json_encode($value);
    }

    public function getIsClosedAttribute()
    {
        return ($this->closes_at && now()->gt($this->closes_at));
    }

    public function getFormPendingSubmissionKeyAttribute()
    {
        if ($this->updated_at?->timestamp) {
            return "openform-" . $this->id . "-pending-submission-" . substr($this->updated_at?->timestamp, -6);
        }
        return null;
    }

    public function getMaxNumberOfSubmissionsReachedAttribute()
    {
        return ($this->max_submissions_count && $this->max_submissions_count <= $this->submissions_count);
    }

    public function setClosedTextAttribute($value)
    {
        $this->attributes['closed_text'] = Purify::clean($value);
    }

    public function setMaxSubmissionsReachedTextAttribute($value)
    {
        $this->attributes['max_submissions_reached_text'] = Purify::clean($value);
    }

    public function getHasPasswordAttribute()
    {
        return !empty($this->password);
    }

    public function getMaxFileSizeAttribute()
    {
        return $this->remember('max_file_size', 15 * 60, function(): int {
            return $this->workspace->max_file_size;
        });
    }

    protected function removedProperties(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return $value ? json_decode($value, true) : [];
            }
        );
    }

    /**
     * Relationships
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function views()
    {
        return $this->hasMany(FormView::class);
    }

    public function statistics()
    {
        return $this->hasMany(FormStatistic::class);
    }

    public function zappierHooks()
    {
        return $this->hasMany(FormZapierWebhook::class);
    }

    /**
     * Config/options
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->doNotGenerateSlugsOnUpdate()
            ->generateSlugsFrom(function (Form $form) {
                return $form->title . ' ' . Str::random(6);
            })
            ->saveSlugsTo('slug');
    }

    public static function newFactory()
    {
        return FormFactory::new();
    }


    public function getNotifiesWebhookAttribute()
    {
        return !empty($this->webhook_url);
    }

    public function getNotifiesDiscordAttribute()
    {
        return !empty($this->discord_webhook_url);
    }

    public function getNotifiesSlackAttribute()
    {
        return !empty($this->slack_webhook_url);
    }
}
