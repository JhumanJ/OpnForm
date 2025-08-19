<?php

namespace App\Service\Forms\Analytics;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserAgentHelper
{
    private $browser;
    private $request;
    private $userAgent;

    public function __construct(Request $request)
    {
        $this->browser = new BrowserDetection();
        $this->request = $request;
        $this->userAgent = $request->userAgent() ?? '';
    }

    public function getMetadata(): array
    {
        // Get browser metadata
        $result = $this->browser->getAll($this->userAgent);

        // Get location info
        $location = $this->getLocation();

        // Return metadata
        return [
            'ip' => $this->request->ip(),
            'source' => ucfirst($this->getTrafficSource()),
            'device' => $result['device_type'],
            'browser' => $result['browser_name'],
            'os' => $result['os_name'],
            'country' => $location['country'] ?? 'Unknown'
        ];
    }

    // Get Traffic Source from Referer
    public function getTrafficSource(): string
    {
        $referer = $this->request->header('Referer');
        $origin = $this->request->header('Origin');
        if (!$referer || str_starts_with($referer, $origin)) {
            return 'Direct';
        }

        $host = parse_url($referer, PHP_URL_HOST);
        if (!$host) {
            return 'Unknown';
        }

        // Match common sources
        $patterns = [
            'google' => '/google\./i',
            'facebook' => '/facebook\.|fb\./i',
            'twitter' => '/twitter\.|t\.co/i',
            'linkedin' => '/linkedin\./i',
            'instagram' => '/instagram\.|ig\./i',
            'youtube' => '/youtube\./i',
            'bing' => '/bing\./i',
            'yahoo' => '/yahoo\./i',
        ];

        foreach ($patterns as $source => $pattern) {
            if (preg_match($pattern, $host)) {
                return ucfirst($source);
            }
        }

        // If no match found, return the domain as the source
        return preg_replace('/^www\./', '', $host);
    }

    // Get Location from IP
    public function getLocation(): array
    {
        if (!config('services.ipinfo.token')) {
            return [];
        }

        $url = 'https://api.ipinfo.io/lite/' . $this->request->ip() . '?token=' . config('services.ipinfo.token');
        $response = Http::get($url);
        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }
}
