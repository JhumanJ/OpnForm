<?php

namespace App\Http\Controllers\Forms\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\FormIntegrationsEventResource;
use App\Models\Integration\FormIntegrationsEvent;

class FormIntegrationsEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(string $id, string $integrationid)
    {
        return FormIntegrationsEventResource::collection(
            FormIntegrationsEvent::where('integration_id', (int)$integrationid)->orderByDesc('created_at')->get()
        );
    }
}
