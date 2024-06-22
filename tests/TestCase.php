<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\PersonalAccessClient;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use TestHelpers;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('passport:keys');

        $clientRepository = new ClientRepository();

        $client = $clientRepository->createPasswordGrantClient(
            null,
            'OpnForm Password Grant Client',
            '/'
        );

        PersonalAccessClient::query()->create([
            'client_id' => $client->id,
        ]);

        Config::set('passport.client_id', $client->id);
    }
}
