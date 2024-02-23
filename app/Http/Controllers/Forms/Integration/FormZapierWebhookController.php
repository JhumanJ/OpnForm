<?php

namespace App\Http\Controllers\Forms\Integration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Integration\StoreFormZapierWebhookRequest;
use App\Models\Integration\FormZapierWebhook;

class FormZapierWebhookController extends Controller
{
    /**
     * Controller for Zappier webhook subscriptions.
     */
    public function __construct()
    {
        //        $this->middleware('subscribed');
        $this->middleware('auth');
    }

    public function store(StoreFormZapierWebhookRequest $request)
    {
        $hook = $request->instanciateHook();
        $this->authorize('store', $hook);

        $hook->save();

        return $this->success([
            'message' => 'Webhook created.',
            'hook' => $hook,
        ]);
    }

    public function delete($id)
    {
        $hook = FormZapierWebhook::findOrFail($id);
        $this->authorize('store', $hook);

        $hook->delete();

        return $this->success([
            'message' => 'Webhook deleted.',
        ]);
    }
}
