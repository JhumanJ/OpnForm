<?php

namespace App\Models\Forms\AI;

use App\Jobs\Form\GenerateAiForm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiFormCompletion extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    protected $table = 'ai_form_completions';

    protected $fillable = [
        'form_prompt',
        'status',
        'result',
        'ip'
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING
    ];

    protected static function booted()
    {
        // Dispatch completion job on creation
        static::created(function (self $completion) {
            GenerateAiForm::dispatch($completion);
        });
    }
}
