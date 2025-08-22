<?php

namespace App\Service;

use App\Models\User;
use App\Models\UserInvite;
use App\Models\Workspace;
use Illuminate\Http\Exceptions\HttpResponseException;

class WorkspaceInviteService
{
    /**
     * Get workspace and role based on provided data
     *
     * @param array $data Must contain 'email' and optionally 'invite_token'
     * @return array [Workspace $workspace, string $role]
     * @throws HttpResponseException
     */
    public function getWorkspaceAndRole(array $data): array
    {
        if (!array_key_exists('invite_token', $data) || empty($data['invite_token'])) {
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
            throw new HttpResponseException(
                response()->json(['message' => 'Invite token is invalid.'], 400)
            );
        }

        if ($userInvite->hasExpired()) {
            throw new HttpResponseException(
                response()->json(['message' => 'Invite token has expired.'], 400)
            );
        }

        if ($userInvite->status == UserInvite::ACCEPTED_STATUS) {
            throw new HttpResponseException(
                response()->json(['message' => 'Invite is already accepted.'], 400)
            );
        }

        $userInvite->markAsAccepted();

        return [
            $userInvite->workspace,
            $userInvite->role,
        ];
    }
}
