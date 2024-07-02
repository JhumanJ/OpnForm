<?php

namespace App\Models\Passport;

use Laravel\Passport\Client as PassportClient;

class Client extends PassportClient
{
    public static function getDefault(): ?static
    {
        return static::query()
            ->findOrFail(config('passport.client_id'));
    }
}
