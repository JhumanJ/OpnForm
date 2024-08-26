<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AppSumoAuthController extends Controller
{
    use AuthenticatesUsers;

    public function handleCallback(Request $request)
    {
        if (! $code = $request->code) {
            return response()->json(['message' => 'Healthy'], 200);
        }
        $accessToken = $this->retrieveAccessToken($code);
        $license = $this->fetchOrCreateLicense($accessToken);

        // If user connected, attach license
        if (Auth::check()) {
            return $this->attachLicense($license);
        }

        // otherwise start login flow by passing the encrypted license key id
        if (is_null($license->user_id)) {
            return redirect(front_url('/register?appsumo_license='.encrypt($license->id)));
        }

        return redirect(front_url('/register?appsumo_error=1'));
    }

    private function retrieveAccessToken(string $requestCode): string
    {
        return Http::withHeaders([
            'Content-type' => 'application/json',
        ])->post('https://appsumo.com/openid/token/', [
            'grant_type' => 'authorization_code',
            'code' => $requestCode,
            'redirect_uri' => route('appsumo.callback'),
            'client_id' => config('services.appsumo.client_id'),
            'client_secret' => config('services.appsumo.client_secret'),
        ])->throw()->json('access_token');
    }

    private function fetchOrCreateLicense(string $accessToken): License
    {
        // Fetch license from API
        $licenseKey = Http::get('https://appsumo.com/openid/license_key/?access_token='.$accessToken)
            ->throw()
            ->json('license_key');

        // Fetch or create license model
        $license = License::where('license_provider', 'appsumo')->where('license_key', $licenseKey)->first();
        if (! $license) {
            $licenseData = Http::withHeaders([
                'X-AppSumo-Licensing-Key' => config('services.appsumo.api_key'),
            ])->get('https://api.licensing.appsumo.com/v2/licenses/'.$licenseKey)->json();

            // Create new license
            $license = License::create([
                'license_key' => $licenseKey,
                'license_provider' => 'appsumo',
                'status' => $licenseData['status'] === 'active' ? License::STATUS_ACTIVE : License::STATUS_INACTIVE,
                'meta' => $licenseData,
            ]);
        }

        return $license;
    }

    private function attachLicense(License $license)
    {
        if (! Auth::check()) {
            throw new AuthenticationException('User not authenticated');
        }

        // Attach license if not already attached
        if (is_null($license->user_id)) {
            $license->user_id = Auth::id();
            $license->save();

            return redirect(front_url('/home?appsumo_connect=1'));
        }

        // Licensed already attached
        return redirect(front_url('/home?appsumo_error=1'));
    }

    /**
     * @return string|null
     *
     * Returns null if no license found
     * Returns true if license was found and attached
     * Returns false if there was an error (license not found or already attached)
     */
    public static function registerWithLicense(User $user, ?string $licenseHash): ?bool
    {
        if (! $licenseHash) {
            return null;
        }
        $licenseId = decrypt($licenseHash);
        $license = License::find($licenseId);

        if ($license && is_null($license->user_id)) {
            $license->user_id = $user->id;
            $license->save();

            return true;
        }

        return false;
    }
}
