<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Templates\CreateTemplateRequest;
use Illuminate\Http\Request;
use App\Models\Template;

class TemplateController extends Controller
{
    public function index()
    {
        // TODO: create resource
        return Template::all();
    }

    public function create(CreateTemplateRequest $request)
    {
        $this->authorize('create', Template::class);

        // Create template
        $template = $request->getTemplate();
        $template->save();

        return $this->success([
            'message' => 'Template created.',
            'template_id' => $template->id
        ]);
    }
}
