<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'license_key',
        'user_id',
        'license_provider',
        'status',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function getMaxFileSizeAttribute(): int
    {
        return [
            1 => 25000000, // 25 MB,
            2 => 50000000, // 50 MB,
            3 => 75000000, // 75 MB,
        ][$this->meta['tier']];
    }

    public function getCustomDomainLimitCountAttribute(): ?int
    {
        return [
            1 => 1,
            2 => 10,
            3 => null,
        ][$this->meta['tier']];
    }

    public static function booted(): void
    {
        static::saved(function (License $license) {
            if ($license->user) {
                $license->user->flushCache();
            }
        });
        static::deleted(function (License $license) {
            if ($license->user) {
                $license->user->flushCache();
            }
        });
    }
}
