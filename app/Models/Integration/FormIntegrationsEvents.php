<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormIntegrationsEvents extends Model
{
    use HasFactory;

    protected $table = 'form_integrations_events';

    protected $fillable = [
        'integration_id',
        'status',
        'data'
    ];

    protected $casts = [
        'data' => 'object'
    ];
}
