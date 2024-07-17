<?php

namespace App\Http\Resources\Zapier;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\Workspace $resource
 */
class WorkspaceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }
}
