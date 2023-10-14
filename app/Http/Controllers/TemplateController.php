<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Templates\FormTemplateRequest;
use App\Http\Resources\FormTemplateResource;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $limit = null;
        if ($request->offsetExists('limit') && $request->get('limit') > 0) {
            $limit = (int) $request->get('limit');
        }

        $templates = Template::where('publicly_listed', true)
            ->when(Auth::check(), function ($query) {
                $query->orWhere('creator_id', Auth::id());
            })
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return FormTemplateResource::collection($templates);
    }

    public function create(FormTemplateRequest $request)
    {
        $this->authorize('create', Template::class);

        // Create template
        $template = $request->getTemplate();
        $template->save();

        return $this->success([
            'message' => 'Template was created.',
            'template_id' => $template->id,
            'data' => new FormTemplateResource($template)
        ]);
    }

    public function update(FormTemplateRequest $request, string $id)
    {
        $template = Template::findOrFail($id);
        $this->authorize('update', $template);

        $template->update($request->all());

        return $this->success([
            'message' => 'Template was updated.',
            'template_id' => $template->id,
            'data' => new FormTemplateResource($template)
        ]);
    }

    public function destroy($id)
    {
        $template = Template::findOrFail($id);
        $this->authorize('delete', $template);

        $template->delete();

        return $this->success([
            'message' => 'Template was deleted.',
        ]);
    }

    public function show(string $slug)
    {
        return new FormTemplateResource(
            Template::whereSlug($slug)->firstOrFail()
        );
    }
}
