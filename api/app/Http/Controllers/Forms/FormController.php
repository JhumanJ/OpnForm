<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormRequest;
use App\Http\Requests\UpdateFormRequest;
use App\Http\Requests\UploadAssetRequest;
use App\Http\Resources\FormResource;
use App\Models\Forms\Form;
use App\Models\Workspace;
use App\Notifications\Forms\MobileEditorEmail;
use App\Service\Forms\FormCleaner;
use App\Service\Storage\StorageFileNameParser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FormController extends Controller
{
    public const ASSETS_UPLOAD_PATH = 'assets/forms';

    private FormCleaner $formCleaner;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['uploadAsset']]);
        $this->formCleaner = new FormCleaner();
    }

    public function index($workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $this->authorize('view', $workspace);
        $this->authorize('viewAny', Form::class);

        $workspaceIsPro = $workspace->is_pro;
        $forms = $workspace->forms()
            ->orderByDesc('updated_at')
            ->paginate(10)->through(function (Form $form) use ($workspace, $workspaceIsPro) {

                // Add attributes for faster loading
                $form->extra = (object) [
                    'loadedWorkspace' => $workspace,
                    'workspaceIsPro' => $workspaceIsPro,
                    'userIsOwner' => true,
                    'cleanings' => $this->formCleaner
                        ->processForm(request(), $form)
                        ->simulateCleaning($workspace)
                        ->getPerformedCleanings(),
                ];

                return $form;
            });

        return FormResource::collection($forms);
    }

    public function show($slug)
    {
        $form = Form::whereSlug($slug)->firstOrFail();
        $this->authorize('view', $form);

        // Add attributes for faster loading
        $workspace = $form->workspace;
        $form->extra = (object)[
            'loadedWorkspace' => $workspace,
            'workspaceIsPro' => $workspace->is_pro,
            'userIsOwner' => true,
            'cleanings' => $this->formCleaner
                ->processForm(request(), $form)
                ->simulateCleaning($workspace)
                ->getPerformedCleanings(),
        ];

        return new FormResource($form);
    }

    /**
     * Return all user forms, used for zapier
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function indexAll()
    {
        $forms = collect();
        foreach (Auth::user()->workspaces as $workspace) {
            $this->authorize('view', $workspace);
            $this->authorize('viewAny', Form::class);

            $workspaceIsPro = $workspace->is_pro;
            $newForms = $workspace->forms()->get()->map(function (Form $form) use ($workspace, $workspaceIsPro) {
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
        $workspace = Workspace::findOrFail($request->get('workspace_id'));
        $this->authorize('view', $workspace);
        $this->authorize('create', [Form::class, $workspace]);

        $formData = $this->formCleaner
            ->processRequest($request)
            ->simulateCleaning($workspace)
            ->getData();

        $form = Form::create(array_merge($formData, [
            'creator_id' => $request->user()->id,
        ]));

        if ($this->formCleaner->hasCleaned()) {
            $formStatus = $form->workspace->is_trialing ? 'Non-trial' : 'Pro';
            $message =  'Form successfully created, but the ' . $formStatus . ' features you used will be disabled when sharing your form:';
        } else {
            $message =  'Form created.';
        }

        return $this->success([
            'message' => $message . ($form->visibility == 'draft' ? ' But other people won\'t be able to see the form since it\'s currently in draft mode' : ''),
            'form' => (new FormResource($form))->setCleanings($this->formCleaner->getPerformedCleanings()),
            'users_first_form' => $request->user()->forms()->count() == 1,
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
            return !Str::of($field['type'])->startsWith('nf-') && !in_array($field['id'], collect($formData['properties'])->pluck('id')->toArray());
        })->toArray());

        $form->update($formData);

        if ($this->formCleaner->hasCleaned()) {
            $formSubscription = $form->is_pro ? 'Enterprise' : 'Pro';
            $formStatus = $form->workspace->is_trialing ? 'Non-trial' : $formSubscription;
            $message = 'Form successfully updated, but the ' . $formStatus . ' features you used will be disabled when sharing your form.';
        } else {
            $message = 'Form updated.';
        }

        return $this->success([
            'message' => $message . ($form->visibility == 'draft' ? ' But other people won\'t be able to see the form since it\'s currently in draft mode' : ''),
            'form' => (new FormResource($form))->setCleanings($this->formCleaner->getPerformedCleanings()),
        ]);
    }

    public function destroy($id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('delete', $form);

        $form->delete();

        return $this->success([
            'message' => 'Form was deleted.',
        ]);
    }

    public function duplicate($id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('update', $form);

        // Create copy
        $formCopy = $form->replicate();
        // generate new slug before changing title
        if (Str::isUuid($formCopy->slug)) {
            $formCopy->slug = Str::uuid();
        } else { // it will generate a new slug
            $formCopy->slug = null;
            $formCopy->save();
        }
        $formCopy->title = 'Copy of ' . $formCopy->title;
        $formCopy->removed_properties = [];
        $formCopy->save();

        return $this->success([
            'message' => 'Form successfully duplicated. You are now editing the duplicated version of the form.',
            'new_form' => new FormResource($formCopy),
        ]);
    }

    public function regenerateLink($id, $option)
    {
        $form = Form::findOrFail($id);
        $this->authorize('update', $form);

        if ($option == 'slug') {
            $form->generateSlug();
        } elseif ($option == 'uuid') {
            $form->slug = Str::uuid();
        }
        $form->save();

        return $this->success([
            'message' => 'Form url successfully updated. Your new form url now is: ' . $form->share_url . '.',
            'form' => new FormResource($form),
        ]);
    }

    /**
     * Upload a form asset
     */
    public function uploadAsset(UploadAssetRequest $request)
    {
        $fileNameParser = StorageFileNameParser::parse($request->url);

        // Make sure we retrieve the file in tmp storage, move it to persistent
        $fileName = PublicFormController::TMP_FILE_UPLOAD_PATH . $fileNameParser->uuid;
        if (!Storage::exists($fileName)) {
            // File not found, we skip
            return null;
        }
        $newPath = self::ASSETS_UPLOAD_PATH . '/' . $fileNameParser->getMovedFileName();
        Storage::move($fileName, $newPath);

        return $this->success([
            'message' => 'File uploaded.',
            'url' => route('forms.assets.show', [$fileNameParser->getMovedFileName()]),
        ]);
    }

    /**
     * File uploads retrieval
     */
    public function viewFile($id, $fileName)
    {
        $form = Form::findOrFail($id);
        $this->authorize('view', $form);

        $path = Str::of(PublicFormController::FILE_UPLOAD_PATH)->replace('?', $form->id) . '/' . $fileName;
        if (!Storage::exists($path)) {
            return $this->error([
                'message' => 'File not found.',
            ]);
        }

        return redirect()->to(Storage::temporaryUrl($path, now()->addMinutes(5)));
    }

    /**
     * Updates a form's workspace
     */
    public function updateWorkspace($id, $workspace_id)
    {
        $form =  Form::findOrFail($id);
        $workspace =  Workspace::findOrFail($workspace_id);

        $this->authorize('update', $form);
        $this->authorize('view', $workspace);

        $form->workspace_id = $workspace_id;
        $form->creator_id = auth()->user()->id;
        $form->save();

        return $this->success([
            'message' => 'Form workspace updated successfully.',
        ]);
    }

    public function mobileEditorEmail($id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('update', $form);

        $form->creator->notify(new MobileEditorEmail($form->slug));

        return $this->success([
            'message' => 'Email sent.',
        ]);
    }
}
