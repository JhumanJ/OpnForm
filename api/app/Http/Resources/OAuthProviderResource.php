<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @property \App\Models\OAuthProvider $resource
 */
class OAuthProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $userId = Auth::id();
        $intention = cache()->get("oauth-intention:{$userId}");

        return [
            'id' => $this->resource->id,
            'provider' => $this->resource->provider,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'intention' => $intention,
            'user' => $this->whenLoaded(
                'user',
                fn () => OAuthProviderUserResource::make($this->resource->user),
                null,
            ),
        ];
    }
}
