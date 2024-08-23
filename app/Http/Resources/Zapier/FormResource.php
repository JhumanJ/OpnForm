<?php

namespace App\Http\Resources\Zapier;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\Forms\Form $resource
 */
class FormResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->title,
            'label' => $this->resource->title . ' (' . $this->resource->slug . ')'
        ];
    }
}
