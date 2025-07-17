<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserBlockRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user_id' => 'required',
            'reason' => 'required|string|max:1000'
        ];
    }
}
