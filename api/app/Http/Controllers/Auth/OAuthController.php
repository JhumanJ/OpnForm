<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Service\OAuth\OAuthFlowOrchestrator;
use Illuminate\Http\Request;

class OAuthController extends Controller
{
    public function __construct(
        private OAuthFlowOrchestrator $flowOrchestrator
    ) {
    }

    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect(Request $request, string $provider)
    {
        $validated = $request->validate([
            'intent' => 'required|in:auth,integration',
            'invite_token' => 'sometimes|string',
            'intention' => 'sometimes|string',
            'autoClose' => 'sometimes|boolean',
            'utm_data' => 'sometimes|array'
        ]);

        $result = $this->flowOrchestrator->processRedirect($provider, $validated);

        return response()->json($result);
    }

    /**
     * Handle the OAuth callback from the provider.
     */
    public function callback(string $provider, Request $request)
    {
        $params = $request->all();
        $result = $this->flowOrchestrator->processCallback($provider, $params);

        return response()->json($result);
    }

    /**
     * Handle widget-based OAuth callback.
     */
    public function handleWidgetCallback(string $service, Request $request)
    {
        $request->validate([
            'intent' => 'required|in:auth,integration',
            'invite_token' => 'sometimes|string',
        ]);

        $result = $this->flowOrchestrator->processWidgetCallback($service, $request);

        return response()->json($result);
    }
}
