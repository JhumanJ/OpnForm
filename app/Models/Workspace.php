<?php

namespace App\Models;

use App\Http\Requests\AnswerFormRequest;
use App\Models\Forms\Form;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    const MAX_FILE_SIZE_FREE = 5000000; // 5 MB
    const MAX_FILE_SIZE_PRO = 50000000; // 50 MB

    const MAX_DOMAIN_PRO = 1;

    protected $fillable = [
        'name',
        'icon',
        'user_id',
        'custom_domain',
    ];

    protected $appends = [
        'is_pro',
        'is_enterprise'
    ];

    protected $casts = [
        'custom_domains' => 'array',
    ];

    public function getIsProAttribute()
    {
        if(is_null(config('cashier.key'))){
            return true;    // If no paid plan so TRUE for ALL
        }

        // Make sure at least one owner is pro
        foreach ($this->owners as $owner) {
            if ($owner->is_subscribed) {
                return true;
            }
        }
        return false;
    }

    public function getMaxFileSizeAttribute()
    {
        if(is_null(config('cashier.key'))){
            return self::MAX_FILE_SIZE_PRO;
        }

        // Return max file size depending on subscription
        foreach ($this->owners as $owner) {
            if ($owner->is_subscribed) {
                if ($license = $owner->activeLicense()) {
                    // In case of special License
                    return $license->max_file_size;
                }
            }
            return self::MAX_FILE_SIZE_PRO;
        }

        return self::MAX_FILE_SIZE_FREE;
    }

    public function getCustomDomainCountLimitAttribute()
    {
        if(is_null(config('cashier.key'))){
            return null;
        }

        // Return max file size depending on subscription
        foreach ($this->owners as $owner) {
            if ($owner->is_subscribed) {
                if ($license = $owner->activeLicense()) {
                    // In case of special License
                    return $license->custom_domain_limit_count;
                }
            }
            return self::MAX_DOMAIN_PRO;
        }

        return 0;
    }

    public function getIsEnterpriseAttribute()
    {
        if(is_null(config('cashier.key'))){
            return true;    // If no paid plan so TRUE for ALL
        }

        foreach ($this->owners as $owner) {
            if ($owner->has_enterprise_subscription) {
                return true;
            }
        }
        return false;
    }

    public function getIsRiskyAttribute()
    {
        // A workspace is risky if all of his users are risky
        foreach ($this->owners as $owner) {
            if (!$owner->is_risky) {
                return false;
            }
        }

        return true;
    }

    public function getSubmissionsCountAttribute()
    {
        $total = 0;
        foreach ($this->forms as $form) {
            $total += $form->submissions_count;
        }

        return $total;
    }

    /**
     * Relationships
     */

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function owners()
    {
        return $this->users()->wherePivot('role', 'admin');
    }

    public function forms()
    {
        return $this->hasMany(Form::class);
    }
}
