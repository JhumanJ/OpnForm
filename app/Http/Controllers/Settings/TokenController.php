<?php

namespace App\Http\Controllers\Settings;

use App\Http\Requests\CreateTokenRequest;
use App\Http\Resources\TokenResource;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class TokenController
{
    public function index()
    {
        return TokenResource::collection(
            Auth::user()->tokens()->get()
        );
    }

    public function store(CreateTokenRequest $request)
    {
        $token = Auth::user()->createToken(
            $request->input('name'),
            $request->input('abilities')
        );

        return response()->json([
            'token' => $token->plainTextToken,
        ]);
    }

    public function destroy(PersonalAccessToken $token)
    {
        $token->delete();

        return response()->json();
    }
}
