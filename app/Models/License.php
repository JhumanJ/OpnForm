<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    use HasFactory;

    protected $fillable = [
        'license_key',
        'user_identifier',
        'license_provider',
        'status',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
