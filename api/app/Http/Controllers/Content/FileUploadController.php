<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Forms\PublicFormController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * Upload file to local temp
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|file']);
        $uuid = (string) Str::uuid();
        $path = $request->file('file')->storeAs(PublicFormController::TMP_FILE_UPLOAD_PATH, $uuid);

        return response()->json([
            'uuid' => $uuid,
            'key' => $path,
        ], 201);
    }
}
