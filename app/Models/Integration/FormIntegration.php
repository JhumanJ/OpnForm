<?php

namespace App\Models\Integration;

use App\Models\Forms\Form;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormIntegration extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_PAUSED = 0;

    use HasFactory;

    protected $fillable = [
        'form_id',
        'status',
        'integration_id',
        'logic',
        'data',
        'oauth_id'
    ];

    protected $casts = [
        'data' => 'object',
        'logic' => 'object'
    ];

    /**
     * Relationships
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public static function getAllIntegrations()
    {
        return json_decode(file_get_contents(resource_path('data/forms/integrations.json')), true);
    }

    public static function getIntegration($key)
    {
        return self::getAllIntegrations()[$key] ?? null;
    }
}
