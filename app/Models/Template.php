<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Stevebauman\Purify\Facades\Purify;

class Template extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'creator_id',
        'name',
        'slug',
        'description',
        'short_description',
        'image_url',
        'structure',
        'questions',
        'publicly_listed',
        'industries',
        'types',
        'related_templates'
    ];

    protected $casts = [
        'structure' => 'array',
        'questions' => 'array',
        'industries' => 'array',
        'types' => 'array',
        'related_templates' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'publicly_listed' => false,
    ];

    protected $appends = [
        'share_url',
    ];

    public function getShareUrlAttribute()
    {
        return url('/form-templates/'.$this->slug);
    }

    public function setDescriptionAttribute($value)
    {
        // Strip out unwanted html
        $this->attributes['description'] = Purify::clean($value);
    }

    public function scopePubliclyListed($query)
    {
        return $this->where('publicly_listed', true);
    }

    /**
     * Config/options
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->doNotGenerateSlugsOnUpdate()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getTypes(): Collection
    {
        return self::getAllTypes()->filter(function ($type) {
            return in_array($type['slug'], $this->types);
        });
    }

    public function getIndustries(): Collection
    {
        return self::getAllIndustries()->filter(function ($type) {
            return in_array($type['slug'], $this->industries);
        });
    }

    public static function getAllTypes(): Collection
    {
        return collect(
            array_values(
                json_decode(
                    file_get_contents(resource_path('data/forms/templates/types.json')),
                    true)
            )
        )->values();
    }

    public static function getAllIndustries(): Collection
    {
        return collect(
            array_values(
                json_decode(
                    file_get_contents(resource_path('data/forms/templates/industries.json')),
                    true)
            )
        )->values();
    }
}
