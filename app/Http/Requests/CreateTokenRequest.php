<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTokenRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
            ],
            'abilities' => [
                'nullable',
                'array'
            ]
        ];
    }
}
