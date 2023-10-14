<?php

namespace App\Http\Requests;

use App\Models\Forms\Form;
use Illuminate\Validation\Rule;

class StoreFormRequest extends UserFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [// Info about database
            'workspace_id' => 'required|exists:workspaces,id',
        ]);
    }
}
