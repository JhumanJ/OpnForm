<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class AppSumoController extends Controller
{
    public function handle(Request $request)
    {
        $this->validateSignature($request);

        if ($request->test) {
            Log::info('[APPSUMO] test request received', $request->toArray());
            return $this->success([
                'message' => 'Webhook received.',
                'event' => $request->event,
                'success' => true,
            ]);
        }

        Log::info('[APPSUMO] request received', $request->toArray());

        // Call the right function depending on the event using match()
        match ($request->event) {
            'activate' => $this->handleActivateEvent($request),
            'upgrade', 'downgrade' => $this->handleChangeEvent($request),
            'deactivate' => $this->handleDeactivateEvent($request),
            default => null,
        };

        return $this->success([
            'message' => 'Webhook received.',
            'event' => $request->event,
            'success' => true,
        ]);
    }

    private function handleActivateEvent($request)
    {
        $licence = License::firstOrNew([
            'license_key' => $request->license_key,
            'license_provider' => 'appsumo',
            'status' => License::STATUS_ACTIVE,
        ]);
        $licence->meta = $request->json()->all();
        $licence->save();
        Log::info('[APPSUMO] activating license', $request->toArray());
    }

    private function handleChangeEvent($request)
    {
        // Deactivate old license
        $oldLicense = License::where([
            'license_key' => $request->prev_license_key,
            'license_provider' => 'appsumo',
        ])->firstOrFail();
        $oldLicense->update([
            'status' => License::STATUS_INACTIVE,
        ]);

        Log::info('[APPSUMO] De-activating license', [
            'license_key' => $request->prev_license_key,
            'license_id' => $oldLicense->id,
        ]);

        // Create new license
        $license = License::create([
            'license_key' => $request->license_key,
            'license_provider' => 'appsumo',
            'status' => License::STATUS_ACTIVE,
            'meta' => $request->json()->all(),
        ]);
        Log::info('[APPSUMO] creating new license',
            [
                'license_key' => $license->license_key,
                'license_id' => $license->id,
            ]);
    }

    private function handleDeactivateEvent($request)
    {
        // Deactivate old license
        $oldLicense = License::where([
            'license_key' => $request->prev_license_key,
            'license_provider' => 'appsumo',
        ])->firstOrFail();
        $oldLicense->update([
            'status' => License::STATUS_INACTIVE,
        ]);
        Log::info('[APPSUMO] De-activating license', [
            'license_key' => $request->prev_license_key,
            'license_id' => $oldLicense->id,
        ]);
    }

    private function validateSignature(Request $request)
    {
        $signature = $request->header('x-appsumo-signature');
        $payload = $request->getContent();

        if ($signature === hash_hmac('sha256', $payload, config('services.appsumo.api_key'))) {
            throw new UnauthorizedException('Invalid signature.');
        }
    }
}
