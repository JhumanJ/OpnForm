<?php

namespace App\Models;

use App\Models\Forms\Form;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Billable;
    use HasFactory;
    use Notifiable;

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
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'photo_url',
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
            $this->name ? urlencode("https://ui-avatars.com/api/$this->name") : 'mp',
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
            || ! is_null($this->activeLicense());
    }

    public function getHasCustomerIdAttribute()
    {
        return ! is_null($this->stripe_id);
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
            'ip' => \Hash::make(request()->ip()),
            'ua' => \Hash::make(request()->userAgent()),
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
            // Remove user's workspace if he's the only one with this workspace
            foreach ($user->workspaces as $workspace) {
                if ($workspace->users()->count() == 1) {
                    $workspace->delete();
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
