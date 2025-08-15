<?php

namespace App\Service;

use Illuminate\Http\Request;

class UserAgentHelper
{

    /**
     * Get the traffic source from the request
     */
    public static function getTrafficSource(Request $request)
    {
        $referer = $request->header('Referer');

        if (!$referer) {
            return 'direct';
        }

        $host = parse_url($referer, PHP_URL_HOST);

        if (!$host) {
            return 'unknown';
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
                return $source;
            }
        }

        // If no match found, return the domain as the source
        return preg_replace('/^www\./', '', $host);
    }

    /**
     * Detect the device type from user agent
     */
    public static function detectDevice(Request $request): string
    {
        $userAgent = $request->userAgent();

        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i', $userAgent)) {
            return 'tablet';
        }

        if (preg_match('/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/', $userAgent)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Detect browser from user agent
     */
    public static function detectBrowser(Request $request): string
    {
        $userAgent = $request->userAgent();

        if (preg_match('/MSIE|Internet Explorer|Trident/i', $userAgent)) {
            return 'Internet Explorer';
        }

        if (preg_match('/Firefox/i', $userAgent)) {
            return 'Firefox';
        }

        if (preg_match('/Chrome/i', $userAgent) && !preg_match('/Edg|Edge|OPR|Opera|SamsungBrowser/i', $userAgent)) {
            return 'Chrome';
        }

        if (preg_match('/Safari/i', $userAgent) && !preg_match('/Chrome|Chromium|Edg|Edge|OPR|Opera|SamsungBrowser/i', $userAgent)) {
            return 'Safari';
        }

        if (preg_match('/Edg|Edge/i', $userAgent)) {
            return 'Edge';
        }

        if (preg_match('/Opera|OPR/i', $userAgent)) {
            return 'Opera';
        }

        if (preg_match('/SamsungBrowser/i', $userAgent)) {
            return 'Samsung Browser';
        }

        return 'Other';
    }

    /**
     * Detect operating system from user agent
     */
    public static function detectOS(Request $request): string
    {
        $userAgent = $request->userAgent();

        if (preg_match('/Windows/i', $userAgent)) {
            return 'Windows';
        }

        if (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
            return 'macOS';
        }

        if (preg_match('/Linux/i', $userAgent) && !preg_match('/Android/i', $userAgent)) {
            return 'Linux';
        }

        if (preg_match('/Android/i', $userAgent)) {
            return 'Android';
        }

        if (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            return 'iOS';
        }

        return 'Other';
    }
}
