<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FontsController extends Controller
{
    public function index(Request $request)
    {
        return \Cache::remember('google_fonts', 60 * 60, function () {
            $url = "https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key=" . config('services.google_fonts_api_key');
            $response = Http::get($url);
            if ($response->successful()) {
                $fonts = collect($response->json()['items'])->filter(function ($font) {
                    return !in_array($font['category'], ['monospace']);
                })->map(function ($font) {
                    return $font['family'];
                })->toArray();
                return response()->json($fonts);
            }

            return [];
        });
    }
}
