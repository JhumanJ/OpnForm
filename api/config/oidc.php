<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default OIDC Scopes
    |--------------------------------------------------------------------------
    |
    | Default scopes to request from the identity provider.
    |
    */
    'default_scopes' => ['openid', 'profile', 'email'],

    /*
    |--------------------------------------------------------------------------
    | Force OIDC Login
    |--------------------------------------------------------------------------
    |
    | When enabled and at least one OIDC connection exists, password-based
    | login will be disabled.
    |
    */
    'force_login' => env('OIDC_FORCE_LOGIN', false),
];
