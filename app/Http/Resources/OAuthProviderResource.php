<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\OAuthProvider $resource
 */
class OAuthProviderResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'provider' => $this->resource->provider,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
        ];
    }
}
