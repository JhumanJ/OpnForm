<?php

namespace App\Http\Controllers\Content;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class UnsplashController extends Controller
{
    public function index(Request $request)
    {
        if (!config('services.unsplash.access_key')) {
            return response()->json([]);
        }

        return Cache::remember('unsplash_images', 1, function () {
            $url = "https://api.unsplash.com/photos?client_id=" . config('services.unsplash.access_key');
            $response = Http::get($url);
            if ($response->successful()) {
                if (isset($response->json()['errors'])) {
                    return response()->json([]);
                }
                return response()->json($response->json());
            }

            return [];
        });
    }
}
