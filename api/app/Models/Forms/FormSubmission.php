<?php

namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'completion_time',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'completion_time' => 'integer',
        ];
    }

    /**
     * RelationShips
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
