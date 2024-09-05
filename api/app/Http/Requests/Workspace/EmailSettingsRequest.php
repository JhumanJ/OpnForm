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
        return [
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'required|string',
            'password' => 'required|string'
        ];
    }
}
