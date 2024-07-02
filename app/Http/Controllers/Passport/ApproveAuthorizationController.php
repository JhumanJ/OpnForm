<?php

namespace App\Http\Controllers\Passport;

use Cache;
use Exception;
use Illuminate\Http\Request;
use Laravel\Passport\Bridge\User;
use Laravel\Passport\Exceptions\InvalidAuthTokenException;
use Laravel\Passport\Http\Controllers\ApproveAuthorizationController as PassportApproveAuthorizationController;
use Nyholm\Psr7\Response as Psr7Response;

class ApproveAuthorizationController extends PassportApproveAuthorizationController
{
    /**
     * Approve the authorization request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request)
    {
        $this->assertValidAuthToken($request);

        $authRequest = $this->getAuthRequestFromSession($request);

        $authRequest->setAuthorizationApproved(true);

        return [
            'redirect' => $this->convertResponse(
                $this->server->completeAuthorizationRequest($authRequest, new Psr7Response())
            )->headers->get('Location')
        ];
    }

    protected function assertValidAuthToken(Request $request)
    {
        $authToken = Cache::get("authToken:{$request->user()->id}");

        if ($request->has('auth_token') && $authToken !== $request->get('auth_token')) {
            Cache::forget("authToken:{$request->user()->id}");
            Cache::forget("authRequest:{$request->user()->id}");

            throw InvalidAuthTokenException::different();
        }
    }

    protected function getAuthRequestFromSession(Request $request)
    {
        return tap(Cache::get("authRequest:{$request->user()->id}"), function ($authRequest) use ($request) {
            if (! $authRequest) {
                throw new Exception('Authorization request was not present in the session.');
            }

            $authRequest->setUser(new User($request->user()->getAuthIdentifier()));
        });
    }
}
