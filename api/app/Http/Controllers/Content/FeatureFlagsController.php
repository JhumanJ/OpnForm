<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;

class FeatureFlagsController extends Controller
{
    public function index()
    {
        $featureFlags = \Cache::remember('feature_flags', 3600, function () {
            return [
                'custom_domains' => config('custom_domains.enabled', false),
                'billing' => [
                    'enabled' => !empty(config('services.stripe.key')) && !empty(config('services.stripe.secret')),
                    'appsumo' => !empty(config('services.appsumo.api_key')) && !empty(config('services.appsumo.api_secret')),
                ],
                'storage' => [
                    'local' => config('filesystems.default') === 'local',
                    's3' => config('filesystems.default') !== 'local',
                ],
                'services' => [
                    'openai' => !empty(config('services.openai.api_key')),
                    'unsplash' => !empty(config('services.unsplash.access_key')),
                    'google' => [
                        'fonts' => !empty(config('services.google.fonts_api_key')),
                        'auth' => !empty(config('services.google.client_id')) && !empty(config('services.google.client_secret')),
                    ],
                ],
                'integrations' => [
                    'zapier' => !empty(config('services.zapier.enabled')),
                    'google_sheets' => !empty(config('services.google.client_id')) && !empty(config('services.google.client_secret')),
                ],
            ];
        });

        return response()->json($featureFlags);
    }
}
