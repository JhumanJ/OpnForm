<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\Integration\FormIntegration $resource
 */
class FormIntegrationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            ...parent::toArray($request),
            'provider' => OAuthProviderResource::make($this->resource->provider),
        ];
    }
}
