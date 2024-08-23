<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Laravel\Sanctum\PersonalAccessToken $resource
 */
class TokenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'abilities' => $this->resource->abilities,
        ];
    }
}
