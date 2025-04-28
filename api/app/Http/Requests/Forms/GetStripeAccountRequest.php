<?php

namespace App\Http\Requests\Forms;

use App\Models\OAuthProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GetStripeAccountRequest extends FormRequest
{
    protected ?OAuthProvider $previewProvider = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Public forms can access this (without oauth_provider_id).
     * Authenticated users can access with oauth_provider_id.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // If oauth_provider_id is present, user must be authenticated
        if ($this->has('oauth_provider_id')) {
            return Auth::check();
        }

        // Otherwise, allow public access (for loading from saved form)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Validate oauth_provider_id only if it's present
            'oauth_provider_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('oauth_providers', 'id')->where(function ($query) {
                    // Ensure the provider belongs to the authenticated user if provided
                    if (Auth::check()) {
                        $query->where('user_id', Auth::id());
                    }
                }),
            ],
        ];
    }

    /**
     * Check if the request is for an editor preview.
     *
     * @return bool
     */
    public function isPreview(): bool
    {
        // It's a preview if the provider ID is present and the user is authenticated
        // The main validation rules will handle invalid/unowned IDs.
        return $this->has('oauth_provider_id') && Auth::check();
    }

    /**
     * Get the validated OAuthProvider for the preview.
     *
     * @return OAuthProvider|null
     */
    public function getPreviewProvider(): ?OAuthProvider
    {
        if (!$this->isPreview()) {
            return null;
        }

        // Cache the loaded provider to avoid redundant queries
        if ($this->previewProvider === null) {
            $this->previewProvider = OAuthProvider::find($this->input('oauth_provider_id'));
        }

        return $this->previewProvider;
    }
}
