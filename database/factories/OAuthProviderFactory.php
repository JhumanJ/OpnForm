<?php

namespace Database\Factories;

use App\Models\OAuthProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class OAuthProviderFactory extends Factory
{
    protected $model = OAuthProvider::class;

    public function definition()
    {
        return [
            'provider' => 'google',
            'provider_user_id' => 'u_test',
            'email' => 'user@example.com',
            'name' => 'user',
            'access_token' => 'ac_test',
            'refresh_token' => 're_test',
        ];
    }
}
