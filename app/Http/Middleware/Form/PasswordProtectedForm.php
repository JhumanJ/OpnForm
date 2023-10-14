<?php

namespace App\Http\Middleware\Form;

use App\Models\Forms\Form;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordProtectedForm
{
    const PASSWORD_HEADER_NAME = 'form-password';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->route('slug')) {
            $form = Form::where('slug',$request->route('slug'))->firstOrFail();
            $request->merge([
                'form' => $form,
            ]);
            $userIsFormOwner = Auth::check() && Auth::user()->workspaces()->find($form->workspace_id) !== null;
            if (!$userIsFormOwner && $form->has_password) {
                if($this->hasCorrectPassword($request, $form)){
                    return $next($request);
                }

                return response([
                    'status' => 'Unauthorized',
                    'message' => 'Form is password protected.',
                ], 403);
            }
        }
        return $next($request);
    }

    public static function hasCorrectPassword(Request $request, Form $form)
    {
        return $request->headers->has(self::PASSWORD_HEADER_NAME) && $request->headers->get(self::PASSWORD_HEADER_NAME) == hash('sha256', $form->password);
    }
}
