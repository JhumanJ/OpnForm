<?php

namespace App\Http\Requests;

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
