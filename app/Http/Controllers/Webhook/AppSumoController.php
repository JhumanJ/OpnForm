<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class AppSumoController extends Controller
{
    public function handle(Request $request)
    {
        $this->validateSignature($request);

        if ($request->test) {
            return $this->success([
                'message' => 'Webhook received.',
                'event' => $request->event,
                'success' => true,
            ]);
        }

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

        // Create new license
        License::create([
            'license_key' => $request->license_key,
            'license_provider' => 'appsumo',
            'status' => License::STATUS_ACTIVE,
            'meta' => $request->json()->all(),
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
