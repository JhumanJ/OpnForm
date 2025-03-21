<?php

namespace App\Models\Forms\AI;

use App\Jobs\Form\GenerateAiForm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiFormCompletion extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $table = 'ai_form_completions';

    protected $fillable = [
        'form_prompt',
        'status',
        'result',
        'ip',
        'error',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    protected static function booted()
    {
        // Dispatch completion job on creation
        static::created(function (self $completion) {
            GenerateAiForm::dispatch($completion);
        });
    }
}
