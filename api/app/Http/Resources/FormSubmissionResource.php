<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionResource extends JsonResource
{
    public bool $publiclyAccessed = false;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $this->generateFileLinks();

        if (!$this->publiclyAccessed) {
            $this->addExtraData();
        }

        return array_merge([
            'data' => $this->data,
            'completion_time' => $this->completion_time,
        ], ($this->publiclyAccessed) ? [] : [
            'form_id' => $this->form_id,
            'id' => $this->id
        ]);
    }

    public function publiclyAccessed($publiclyAccessed = true)
    {
        $this->publiclyAccessed = $publiclyAccessed;
        return $this;
    }

    private function addExtraData()
    {
        $this->data = array_merge($this->data, [
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'id' => $this->id,
        ]);
    }

    /**
     * Link to the file (generating signed s3 URL)
     *
     * @return void
     */
    private function generateFileLinks()
    {
        $data = $this->data;
        $formFields = collect($this->form->properties)->concat(collect($this->form->removed_properties));
        $fileFields = $formFields->filter(function ($field) {
            return in_array($field['type'], ['files', 'signature']);
        });
        foreach ($fileFields as $field) {
            if (isset($data[$field['id']]) && !empty($data[$field['id']])) {
                $data[$field['id']] = collect($data[$field['id']])->filter(function ($file) {
                    return $file !== null && $file;
                })->map(function ($file) {
                    return [
                        'file_url' => \URL::signedRoute(
                            'open.forms.submissions.file',
                            [$this->form_id, $file],
                            now()->addMinutes(10)
                        ),
                        'file_name' => $file,
                    ];
                });
            }
        }
        $this->data = $data;
    }
}
