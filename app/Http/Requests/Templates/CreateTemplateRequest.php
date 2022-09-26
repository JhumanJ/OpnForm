<?php

namespace App\Http\Requests\Templates;

use App\Models\Template;
use Illuminate\Foundation\Http\FormRequest;

class CreateTemplateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'form' => 'required|array',
            'name' => 'required|string|max:60',
            'slug' => 'required|string|unique:templates',
            'description' => 'required|string|max:2000',
            'image_url' => 'required|string',
        ];
    }

    public function getTemplate() : Template
    {
        $structure = $this->form;
        $ignoreKeys = ['id','creator','creator_id','created_at','updated_at','extra','workspace','submissions','submissions_count','views','views_count'];
        foreach($structure as $key=>$val){
            if(in_array($key, $ignoreKeys)){
                $structure[$key] = null;
            }
        }
        return new Template([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'structure' => $structure
        ]);
    }
}
