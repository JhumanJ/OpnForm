<?php

namespace App\Enums;

use Illuminate\Support\Arr;

enum AccessTokenAbility: string
{
    case ManageIntegrations = 'manage-integrations';
        // Granular scopes
        // Forms
    case FormsRead = 'forms-read';
    case FormsWrite = 'forms-write';

        // Submissions
    case SubmissionsRead = 'submissions-read';
    case SubmissionsWrite = 'submissions-write';

        // Workspaces
    case WorkspacesRead = 'workspaces-read';
    case WorkspacesWrite = 'workspaces-write';

        // Workspace Users
    case WorkspaceUsersRead = 'workspace-users-read';
    case WorkspaceUsersWrite = 'workspace-users-write';

    public static function values(): array
    {
        return array_map(
            fn(AccessTokenAbility $case) => $case->value,
            static::cases()
        );
    }

    public static function allowed(array $abilities): array
    {
        return Arr::where(
            $abilities,
            fn(string $ability) => in_array($ability, static::values())
        );
    }
}
