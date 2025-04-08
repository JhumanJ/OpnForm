<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class FeatureFlagsController extends Controller
{
    public function index()
    {
        $featureFlags = Cache::remember('feature_flags', 3600, function () {
            return [
                'self_hosted' => config('app.self_hosted', true),
                'custom_domains' => config('custom-domains.enabled', false),
                'ai_features' => !empty(config('services.openai.api_key')),

                'billing' => [
                    'enabled' => !empty(config('cashier.key')) && !empty(config('cashier.secret')),
                    'appsumo' => !empty(config('services.appsumo.api_key')) && !empty(config('services.appsumo.api_secret')),
                ],
                'storage' => [
                    'local' => config('filesystems.default') === 'local',
                    's3' => config('filesystems.default') !== 'local',
                ],
                'services' => [
                    'unsplash' => !empty(config('services.unsplash.access_key')),
                    'google' => [
                        'fonts' => !empty(config('services.google.fonts_api_key')),
                        'auth' => !empty(config('services.google.client_id')) && !empty(config('services.google.client_secret')),
                    ],
                    'telegram' => [
                        'bot_id' => config('services.telegram.bot_id') ?? false
                    ]
                ],
                'integrations' => [
                    'zapier' => config('services.zapier.enabled'),
                    'google_sheets' => !empty(config('services.google.client_id')) && !empty(config('services.google.client_secret')),
                    'telegram' => !empty(config('services.telegram.bot_id')) && !empty(config('services.telegram.bot_token')),
                ],
            ];
        });

        return response()->json($featureFlags);
    }
}
