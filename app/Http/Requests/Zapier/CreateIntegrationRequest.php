<?php

namespace App\Http\Requests\Zapier;

use App\Models\Forms\Form;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateIntegrationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'form_id' => [
                'required',
                Rule::exists(Form::getModel()->getTable(), 'id'),
            ],
            'hookUrl' => [
                'required',
                'url',
            ],
        ];
    }

    public function form(): Form
    {
        return Form::findOrFail($this->input('form_id'));
    }
}
