<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Facades\Purify;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'structure',
        'questions',
    ];

    protected $casts = [
        'structure' => 'array',
        'questions' => 'array',
    ];

    public function setDescriptionAttribute($value)
    {
        // Strip out unwanted html
        $this->attributes['description'] = Purify::clean($value);
    }
}
