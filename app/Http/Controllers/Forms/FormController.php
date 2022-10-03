<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormRequest;
use App\Http\Requests\UpdateFormRequest;
use App\Http\Requests\UploadAssetRequest;
use App\Http\Resources\FormResource;
use App\Models\Forms\Form;
use App\Models\Workspace;
use App\Service\Forms\FormCleaner;
use App\Service\Storage\StorageFileNameParser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FormController extends Controller
{
    const ASSETS_UPLOAD_PATH = 'assets/forms';

    private FormCleaner $formCleaner;

    public function __construct()
    {
        $this->middleware('auth');
        $this->formCleaner = new FormCleaner();
    }

    public function index($workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $this->authorize('view', $workspace);
        $this->authorize('viewAny', Form::class);

        $workspaceIsPro = $workspace->is_pro;
        $forms = $workspace->forms()->with(['creator','views','submissions'])->paginate(10)->through(function (Form $form) use ($workspace, $workspaceIsPro){
            // Add attributes for faster loading
            $form->extra = (object) [
                'loadedWorkspace' => $workspace,
                'workspaceIsPro' => $workspaceIsPro,
                'userIsOwner' => true,
            ];
            return $form;
        });
        return FormResource::collection($forms);
    }

    /**
     * Return all user forms, used for zapier
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function indexAll()
    {
        $forms = collect();
        foreach (Auth::user()->workspaces as $workspace) {
            $this->authorize('view', $workspace);
            $this->authorize('viewAny', Form::class);

            $workspaceIsPro = $workspace->is_pro;
            $newForms = $workspace->forms()->with(['creator','views','submissions'])->get()->map(function (Form $form) use ($workspace, $workspaceIsPro){
                // Add attributes for faster loading
                $form->extra = (object) [
                    'loadedWorkspace' => $workspace,
                    'workspaceIsPro' => $workspaceIsPro,
                    'userIsOwner' => true,
                ];
                return $form;
            });

            $forms = $forms->merge($newForms);
        }
        return FormResource::collection($forms);
    }

    public function store(StoreFormRequest $request)
    {
        $this->authorize('create', Form::class);

        $workspace = Workspace::findOrFail($request->get('workspace_id'));
        $this->authorize('view', $workspace);

        $formData = $this->formCleaner
            ->processRequest($request)
            ->simulateCleaning($workspace)
            ->getData();

        $form = Form::create(array_merge($formData, [
            'creator_id' => $request->user()->id
        ]));

        return $this->success([
            'message' => $this->formCleaner->hasCleaned() ? 'Form successfully created, but the Pro features you used will be disabled when sharing your form:' : 'Form created.',
            'form_cleaning' => $this->formCleaner->getPerformedCleanings(),
            'form' => new FormResource($form),
            'users_first_form' => $request->user()->forms()->count() == 1
        ]);
    }

    public function update(UpdateFormRequest $request, string $id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('update', $form);

        $formData = $this->formCleaner
            ->processRequest($request)
            ->simulateCleaning($form->workspace)
            ->getData();
        
        // Set Removed Properties
        $formData['removed_properties'] = array_merge($form->removed_properties, collect($form->properties)->filter(function ($field) use ($formData) {
            return (!Str::of($field['type'])->startsWith('nf-') && !in_array($field['id'], collect($formData['properties'])->pluck("id")->toArray()));
        })->toArray());

        $form->update($formData);

        return $this->success([
            'message' => $this->formCleaner->hasCleaned() ? 'Form successfully updated, but the Pro features you used will be disabled when sharing your form:' : 'Form updated.',
            'form_cleaning' => $this->formCleaner->getPerformedCleanings(),
            'form' => new FormResource($form)
        ]);
    }

    public function destroy($id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('delete', $form);

        $form->delete();
        return $this->success([
            'message' => 'Form was deleted.'
        ]);
    }

    public function duplicate($id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('update', $form);

        // Create copy
        $formCopy = $form->replicate();
        $formCopy->title = 'Copy of '.$formCopy->title;
        $formCopy->save();

        return $this->success([
            'message' => 'Form successfully duplicated.',
            'new_form' => new FormResource($formCopy)
        ]);
    }

    public function regenerateLink($id, $option)
    {
        $form = Form::findOrFail($id);
        $this->authorize('update', $form);

        if ( $option == 'slug') {
            $form->generateSlug();
        } elseif ($option == 'uuid') {
            $form->slug = Str::uuid();
        }
        $form->save();

        return $this->success([
            'message' => 'Form url successfully updated. Your new form url now is: '.$form->share_url.'.',
            'form' => new FormResource($form)
        ]);
    }

    /**
     * Upload a form asset
     */
    public function uploadAsset(UploadAssetRequest $request)
    {
        $this->authorize('viewAny', Form::class);

        $fileNameParser = StorageFileNameParser::parse($request->url);

        // Make sure we retrieve the file in tmp storage, move it to persistent
        $fileName = PublicFormController::TMP_FILE_UPLOAD_PATH.'/'.$fileNameParser->uuid;;
        if (!Storage::disk('s3')->exists($fileName)) {
            // File not found, we skip
            return null;
        }
        $newPath = self::ASSETS_UPLOAD_PATH.'/'.$fileNameParser->getMovedFileName();
        Storage::disk('s3')->move($fileName, $newPath);

        return $this->success([
            'message' => 'File uploaded.',
            'url' => route("forms.assets.show", [$fileNameParser->getMovedFileName()])
        ]);
    }

    /**
     * File uploads retrieval
     */
    public function viewFile($id, $fileName)
    {
        $form = Form::findOrFail($id);
        $this->authorize('view', $form);

        $path = Str::of(PublicFormController::FILE_UPLOAD_PATH)->replace('?', $form->id).'/'.$fileName;
        if (!Storage::disk('s3')->exists($path)) {
            return $this->error([
                'message' => 'File not found.'
            ]);
        }

        return redirect()->to(Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(5)));
    }
}
