<?php

namespace App\Models;

use App\Models\Forms\Form;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Billable;
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';
    public const ROLE_READONLY = 'readonly';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
        self::ROLE_READONLY,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'hear_about_us',
        'utm_data',
        'meta',
        'blocked_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'hear_about_us',
        'meta'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected function casts()
    {
        return [
            'email_verified_at' => 'datetime',
            'utm_data' => 'array',
            'meta' => 'array',
            'blocked_at' => 'datetime',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'photo_url',
        'is_blocked'
    ];

    public function ownsForm(Form $form)
    {
        return $this->workspaces()->where('workspaces.id', $form->workspace_id)->exists();
    }

    public function ownsWorkspace(Workspace $workspace)
    {
        return $this->workspaces()->where('workspaces.id', $workspace->id)->exists();
    }

    /**
     * Get the profile photo URL attribute.
     *
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
        return vsprintf('https://www.gravatar.com/avatar/%s.jpg?s=200&d=%s', [
            md5(strtolower($this->email)),
            $this->name ? urlencode("https://ui-avatars.com/api/$this->name.jpg") : 'mp',
        ]);
    }

    public function getHasFormsAttribute()
    {
        return $this->workspaces()->whereHas('forms')->exists();
    }

    public function getIsSubscribedAttribute()
    {
        return $this->subscribed()
            || in_array($this->email, config('opnform.extra_pro_users_emails'))
            || !is_null($this->activeLicense());
    }

    public function getHasCustomerIdAttribute()
    {
        return !is_null($this->stripe_id);
    }

    public function getAdminAttribute()
    {
        return in_array($this->email, config('opnform.admin_emails'));
    }

    public function getModeratorAttribute()
    {
        return in_array($this->email, config('opnform.moderator_emails')) || $this->admin;
    }

    public function getTemplateEditorAttribute()
    {
        return $this->admin || in_array($this->email, config('opnform.template_editor_emails'));
    }

    public function getIsProAttribute()
    {
        return $this->workspaces()->get()->some(function ($workspace) {
            return $workspace->is_pro;
        });
    }

    public function getIsBlockedAttribute()
    {
        return !is_null($this->blocked_at);
    }

    public function blockUser(string $reason, ?int $moderatorId): void
    {
        $this->blocked_at = now();
        $history = $this->meta['blocking_history'] ?? [];
        $history[] = [
            'reason' => $reason,
            'blocked_at' => $this->blocked_at,
            'blocked_by' => $moderatorId ?? null,
            'unblock_reason' => null,
            'unblocked_at' => null,
            'unblocked_by' => null,
        ];
        $this->meta = array_merge($this->meta ?? [], ['blocking_history' => $history]);
        $this->save();
    }

    public function unblockUser(string $reason, int $moderatorId): void
    {
        $this->blocked_at = null;
        $history = $this->meta['blocking_history'] ?? [];
        if (empty($history)) {
            $this->save();
            return;
        }

        $lastBlockKey = array_key_last($history);
        $history[$lastBlockKey]['unblock_reason'] = $reason;
        $history[$lastBlockKey]['unblocked_at'] = now();
        $history[$lastBlockKey]['unblocked_by'] = $moderatorId;

        $this->meta = array_merge($this->meta ?? [], ['blocking_history' => $history]);
        $this->save();
    }

    public function getLastBlock(): ?array
    {
        $history = $this->meta['blocking_history'] ?? [];
        if (empty($history)) {
            return null;
        }

        return end($history);
    }

    /**
     * =================================
     *  Helper Related
     * =================================
     */

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }

    /**
     * =================================
     *  Relationship
     * =================================
     */
    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class, 'creator_id');
    }

    public function formTemplates()
    {
        return $this->hasMany(Template::class, 'creator_id');
    }

    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    public function activeLicense(): ?License
    {
        return $this->licenses()->active()->first();
    }

    /**
     * =================================
     *  Oauth Related
     * =================================
     */

    /**
     * Get the oauth providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'ip' => Hash::make(request()->ip()),
            'ua' => Hash::make(request()->userAgent()),
        ];
    }

    public function getIsRiskyAttribute()
    {
        return $this->created_at->isAfter(now()->subDays(3)) || // created in last 3 days
            $this->subscriptions()->where(function ($q) {
                $q->where('stripe_status', 'trialing')
                    ->orWhere('stripe_status', 'active');
            })->first()?->onTrial();
    }

    public function flushCache()
    {
        $this->workspaces()->with('forms')->get()->each(function (Workspace $workspace) {
            $workspace->flush();
            $workspace->forms->each(function (Form $form) {
                $form->flush();
            });
        });
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function (User $user) {
            // Delete all OAuth providers for this user
            $user->oauthProviders()->delete();

            // Remove user's workspace if he's the only one with this workspace
            foreach ($user->workspaces as $workspace) {
                if ($workspace->users()->count() == 1) {
                    $workspace->delete();
                } else {
                    $workspace->users()->detach($user->id);
                }
            }
        });
    }

    public function scopeWithActiveSubscription($query)
    {
        return $query->whereHas('subscriptions', function ($query) {
            $query->where(function ($q) {
                $q->where('stripe_status', 'trialing')
                    ->orWhere('stripe_status', 'active');
            });
        });
    }
}
