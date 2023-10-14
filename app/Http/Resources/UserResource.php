<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $personalData = \Auth::id() === $this->id ? [
            'is_subscribed' => $this->is_subscribed,
            'has_enterprise_subscription' => $this->has_enterprise_subscription,
            'admin' => $this->admin,
            'template_editor' => $this->template_editor,
            'has_customer_id' => $this->has_customer_id,
            'has_forms' => $this->has_forms,
        ] : [];

        return array_merge(parent::toArray($request), $personalData);
    }
}
