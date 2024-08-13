<?php

namespace App\Http\Controllers;

use App\Http\Requests\Templates\FormTemplateRequest;
use App\Http\Resources\FormTemplateResource;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $limit = (int) $request->get('limit', 0);
        $onlyMy = (bool) $request->get('onlymy', false);

        $query = Template::query();

        if (Auth::check()) {
            if ($onlyMy) {
                $query->where('creator_id', Auth::id());
            } else {
                $query->where(function ($q) {
                    $q->where('publicly_listed', true)
                      ->orWhere('creator_id', Auth::id());
                });
            }
        } else {
            $query->where('publicly_listed', true);
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $templates = $query->orderByDesc('created_at')->get();

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
            'data' => new FormTemplateResource($template),
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
            'data' => new FormTemplateResource($template),
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
