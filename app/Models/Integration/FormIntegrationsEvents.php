<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormIntegrationsEvents extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'form_integrations_events';

    protected $fillable = [
        'integration_id',
        'status',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];
}
