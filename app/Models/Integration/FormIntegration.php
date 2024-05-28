<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Forms\Form;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormIntegration extends Model
{
    use HasFactory;
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'form_id',
        'status',
        'integration_id',
        'logic',
        'data',
        'oauth_id'
    ];

    protected function casts(): array
    {
        return [
            'data' => 'object',
            'logic' => 'object'
        ];
    }

    /**
     * Relationships
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(FormIntegrationsEvent::class, 'integration_id');
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
