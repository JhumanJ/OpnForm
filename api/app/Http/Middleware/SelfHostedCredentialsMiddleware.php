<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class SelfHostedCredentialsMiddleware
{
    public const ALLOWED_ROUTES = [
        'login',
        'credentials.update',
        'user.current',
        'logout',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('testing')) {
            return $next($request);
        }

        if (in_array($request->route()->getName(), self::ALLOWED_ROUTES)) {
            return $next($request);
        }

        if (
            config('app.self_hosted') &&
            $request->user() &&
            !$this->isInitialSetupComplete()
        ) {
            return response()->json([
                'message' => 'You must change your credentials when in self-hosted mode',
                'type' => 'error',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }

    private function isInitialSetupComplete(): bool
    {
        return (bool) Cache::remember('initial_user_setup_complete', 60 * 60, function () {
            $maxUserId = $this->getMaxUserId();
            if ($maxUserId === 0) {
                return false;
            }
            return !User::where('email', 'admin@opnform.com')->exists();
        });
    }

    private function getMaxUserId(): int
    {
        return (int) Cache::remember('max_user_id', 60 * 60, function () {
            return User::max('id') ?? 0;
        });
    }
}
