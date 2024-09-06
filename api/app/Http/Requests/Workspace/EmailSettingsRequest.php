<?php

namespace App\Http\Requests\Workspace;

use App\Models\Workspace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class EmailSettingsRequest extends FormRequest
{
    public Workspace $workspace;

    public function __construct(Request $request, Workspace $workspace)
    {
        $this->workspace = Workspace::findOrFail($request->workspaceId);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $allFieldsPresent = $this->filled(['host', 'port', 'username', 'password']);

        return [
            'host' => [
                $allFieldsPresent ? 'required' : 'nullable',
                'required_with:port,username,password',
                'string',
            ],
            'port' => [
                $allFieldsPresent ? 'required' : 'nullable',
                'required_with:host,username,password',
                'integer',
            ],
            'username' => [
                $allFieldsPresent ? 'required' : 'nullable',
                'required_with:host,port,password',
                'string',
            ],
            'password' => [
                $allFieldsPresent ? 'required' : 'nullable',
                'required_with:host,port,username',
                'string',
            ],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'host.required_with' => 'The host field is required.',
            'port.required_with' => 'The port field is required.',
            'username.required_with' => 'The username field is required.',
            'password.required_with' => 'The password field is required.',
        ];
    }
}
