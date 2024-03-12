<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormIntegrationsEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'integration_id',
        'status',
        'data'
    ];

    protected $casts = [
        'data' => 'object'
    ];
}
