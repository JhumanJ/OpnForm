<?php

namespace App\Http\Resources;

use App\Http\Requests\Templates\CreateTemplateRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        foreach (CreateTemplateRequest::IGNORED_KEYS as $key) {
            unset($data[$key]);
        }
        return $data;
    }
}
