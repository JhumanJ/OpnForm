<?php

namespace App\Http\Controllers\Passport;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Http\Controllers\AuthorizationController as PassportAuthorizationController;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizationController extends PassportAuthorizationController
{
    public function __construct(
        AuthorizationServer $server,
        Guard $guard
    ) {
        $this->server = $server;
        $this->guard = $guard;
    }

    public function authorize(
        ServerRequestInterface $psrRequest,
        Request $request,
        ClientRepository $clients,
        TokenRepository $tokens
    ) {
        $authRequest = $this->withErrorHandling(function () use ($psrRequest) {
            return $this->server->validateAuthorizationRequest($psrRequest);
        });

        if ($this->guard->guest()) {
            return $request->get('prompt') === 'none'
                    ? $this->denyRequest($authRequest)
                    : $this->promptForLogin($request);
        }

        if ($request->get('prompt') === 'login' &&
            ! $request->session()->get('promptedForLogin', false)) {
            $this->guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->promptForLogin($request);
        }

        $request->session()->forget('promptedForLogin');

        $scopes = $this->parseScopes($authRequest);
        $user = $this->guard->user();
        $client = $clients->find($authRequest->getClient()->getIdentifier());

        if ($request->get('prompt') !== 'consent' &&
            ($client->skipsAuthorization() || $this->hasValidToken($tokens, $user, $client, $scopes))) {
            return $this->approveRequest($authRequest, $user);
        }

        if ($request->get('prompt') === 'none') {
            return $this->denyRequest($authRequest, $user);
        }

        $request->session()->put('authToken', $authToken = Str::random());
        $request->session()->put('authRequest', $authRequest);

        return response()->json([
            'client' => $client,
            'user' => $user,
            'scopes' => $scopes,
            'request' => $request,
            'authToken' => $authToken,
        ]);
    }
}
