<?php

namespace App\Enums;

use Illuminate\Support\Arr;

enum AccessTokenAbility: string
{
    case ManageIntegrations = 'manage-integrations';
    case ListForms = 'list-forms';
    case ListWorkspaces = 'list-workspaces';

    public static function values(): array
    {
        return array_map(
            fn (AccessTokenAbility $case) => $case->value,
            static::cases()
        );
    }

    public static function allowed(array $abilities): array
    {
        return Arr::where(
            $abilities,
            fn (string $ability) => in_array($ability, static::values())
        );
    }
}
