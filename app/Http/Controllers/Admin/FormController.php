<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use App\Models\User;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function getDeletedForms($userId)
    {
        $user  = User::find($userId);
        $deletedForms = $user->forms()->with('creator')->onlyTrashed()->get()->map(function ($form) {
            return  [
                "id" => $form->id,
                "slug" => $form->slug,
                "title" => $form->title,
                "created_by" => $form->creator->email,
                "deleted_at" => $form->deleted_at->format('Y-m-d'),
            ];
        });
        return $this->success(['forms' =>  $deletedForms]);
    }

    public function restoreDeletedForm(string $slug)
    {
        $form = Form::withTrashed()->whereSlug($slug)->firstOrFail();
        $form->restore();
        return  $this->success(['message' => 'Form restored successfully']);
    }
}
