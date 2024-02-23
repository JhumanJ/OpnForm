<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $this->createLicense($request->json()->all());
    }

    private function handleChangeEvent($request)
    {
        $license = $this->deactivateLicense($request->prev_license_key);
        $this->createLicense(array_merge($request->json()->all(), [
            'user_id' => $license->user_id,
        ]));
    }

    private function handleDeactivateEvent($request)
    {
        $this->deactivateLicense($request->license_key);
    }

    private function createLicense(array $licenseData): License
    {
        $license = License::firstOrNew([
            'license_key' => $licenseData['license_key'],
            'license_provider' => 'appsumo',
            'status' => License::STATUS_ACTIVE,
        ]);
        $license->meta = $licenseData;
        $license->user_id = $licenseData['user_id'] ?? null;
        $license->save();

        Log::info(
            '[APPSUMO] creating new license',
            [
                'license_key' => $license->license_key,
                'license_id' => $license->id,
            ]
        );

        return $license;
    }

    private function deactivateLicense(string $licenseKey): License
    {
        $license = License::where([
            'license_key' => $licenseKey,
            'license_provider' => 'appsumo',
        ])->firstOrFail();
        $license->update([
            'status' => License::STATUS_INACTIVE,
        ]);
        Log::info('[APPSUMO] De-activating license', [
            'license_key' => $licenseKey,
            'license_id' => $license->id,
        ]);

        return $license;
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
