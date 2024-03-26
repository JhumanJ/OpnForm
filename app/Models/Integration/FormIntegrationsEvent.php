<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormIntegrationsEvent extends Model
{
    use HasFactory;

    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR = 'error';

    protected $fillable = [
        'integration_id',
        'status',
        'data'
    ];

    protected $casts = [
        'data' => 'object'
    ];

    public function integration()
    {
        return $this->belongsTo(FormIntegration::class, 'integration_id');
    }
}
