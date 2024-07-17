<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserInvite;
use App\Models\Workspace;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    use RegistersUsers;

    private ?bool $appsumoLicense = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * The user has been registered.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function registered(Request $request, User $user)
    {
        if ($user instanceof MustVerifyEmail) {
            return response()->json(['status' => trans('verification.sent')]);
        }

        return response()->json(array_merge(
            (new UserResource($user))->toArray($request),
            [
                'appsumo_license' => $this->appsumoLicense,
            ]
        ));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email:filter|max:255|unique:users|indisposable',
            'password' => 'required|min:6|confirmed',
            'hear_about_us' => 'required|string',
            'agree_terms' => ['required', Rule::in([true])],
            'appsumo_license' => ['nullable'],
            'invite_token' => ['nullable', 'string'],
        ], [
            'agree_terms' => 'Please agree with the terms and conditions.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        $this->checkRegistrationAllowed($data);
        [$workspace, $role] = $this->getWorkspaceAndRole($data);

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => bcrypt($data['password']),
            'hear_about_us' => $data['hear_about_us'],
        ]);

        // Add relation with user
        $user->workspaces()->sync([
            $workspace->id => [
                'role' => $role,
            ],
        ], false);

        $this->appsumoLicense = AppSumoAuthController::registerWithLicense($user, $data['appsumo_license'] ?? null);

        return $user;
    }

    private function checkRegistrationAllowed(array $data)
    {
        if (config('app.self_hosted') && !array_key_exists('invite_token', $data)) {
            response()->json(['message' => 'Registration is not allowed in self host mode'], 400)->throwResponse();
        }
    }

    private function getWorkspaceAndRole(array $data)
    {
        if (!array_key_exists('invite_token', $data)) {
            return [
                Workspace::create([
                    'name' => 'My Workspace',
                    'icon' => '🧪',
                ]),
                User::ROLE_ADMIN
            ];
        }

        $userInvite = UserInvite::where('email', $data['email'])
            ->where('token', $data['invite_token'])
            ->first();

        if (!$userInvite) {
            response()->json(['message' => 'Invite token is invalid.'], 400)->throwResponse();
        }
        if ($userInvite->hasExpired()) {
            response()->json(['message' => 'Invite token has expired.'], 400)->throwResponse();
        }

        if ($userInvite->status == UserInvite::ACCEPTED_STATUS) {
            response()->json(['message' => 'Invite is already accepted.'], 400)->throwResponse();
        }

        $userInvite->markAsAccepted();
        return [
            $userInvite->workspace,
            $userInvite->role,
        ];
    }
}
