<?php

namespace App\Http\Controllers;

use App\Http\Resources\VersionResource;
use App\Models\Forms\Form;
use App\Models\Version;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    protected $modelAliases = [
        'form' => Form::class
    ];

    protected function getModelClass(string $alias): string
    {
        if (!isset($this->modelAliases[$alias])) {
            return $this->error([
                'success' => false,
                'message' => 'Invalid model alias',
            ]);
        }
        return $this->modelAliases[$alias];
    }

    public function index(Request $request, $modelType, $id)
    {
        $model = $this->getModelClass($modelType);
        if ($model instanceof \Illuminate\Http\JsonResponse) {
            return $model;
        }
        $modelInstance = new $model();
        $modelRecord = $modelInstance::findOrFail($id);
        $versions = $modelRecord->versions()->with('user')->orderBy('created_at', 'desc')->get();
        return VersionResource::collection($versions);
    }

    public function restore(Request $request, $versionId)
    {
        $version = Version::findOrFail($versionId);
        $user = $version->user;
        if (!$user->is_pro) {
            return $this->error([
                'message' => 'You need to be a Pro user to restore this version',
            ]);
        }

        $version->revert();

        return $this->success([
            'message' => 'Version restored successfully. Please publish form to save the changes.',
        ]);
    }
}
