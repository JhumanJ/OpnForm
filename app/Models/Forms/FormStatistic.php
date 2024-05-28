<?php

namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormStatistic extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'form_id',
        'data',
        'date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    /**
     * Relationships
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
