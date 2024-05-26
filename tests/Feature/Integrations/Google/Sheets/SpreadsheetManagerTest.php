<?php

use App\Integrations\Data\SpreadsheetData;
use App\Integrations\Google\Google;
use App\Integrations\Google\Sheets\SpreadsheetManager;
use App\Models\Integration\FormIntegration;
use App\Models\OAuthProvider;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

test('build columns', function () {
    /** @var \App\Models\User $user */
    $user = $this->createUser();

    /** @var \App\Models\Workspace $workspace */
    $workspace = $this->createUserWorkspace($user);

    /** @var \App\Models\Forms $form */
    $form = $this->createForm($user, $workspace);

    /** @var \App\Models\OAuthProvider $provider */
    $provider = OAuthProvider::factory()
        ->for($user)
        ->create();

    /** @var FormIntegration $integration */
    $integration = FormIntegration::factory()
        ->for($form)
        ->for($provider, 'provider')
        ->create([
            'data' => new SpreadsheetData(
                url: 'https://google.com',
                spreadsheet_id: 'sp_test',
                columns: []
            )
        ]);

    $google = new Google($integration);

    $manager = new SpreadsheetManager($google, $integration);

    $columns = $manager->buildColumns();

    assertCount(14, $columns);

    foreach($columns as $key => $column) {
        assertEquals($form->properties[$key]['id'], $column['id']);
        assertEquals($form->properties[$key]['name'], $column['name']);
    }
});

test('update columns', function () {
    /** @var \App\Models\User $user */
    $user = $this->createUser();

    /** @var \App\Models\Workspace $workspace */
    $workspace = $this->createUserWorkspace($user);

    /** @var \App\Models\Forms $form */
    $form = $this->createForm($user, $workspace);

    $form->update([
        'properties' => [
            ['id' => '000', 'name' => 'First'],
            ['id' => '001', 'name' => 'Second'],
        ]
    ]);

    /** @var \App\Models\OAuthProvider $provider */
    $provider = OAuthProvider::factory()
        ->for($user)
        ->create();

    /** @var FormIntegration $integration */
    $integration = FormIntegration::factory()
        ->for($form)
        ->for($provider, 'provider')
        ->create([
            'data' => new SpreadsheetData(
                url: 'https://google.com',
                spreadsheet_id: 'sp_test',
                columns: [
                    ['id' => '000', 'name' => 'First'],
                    ['id' => '001', 'name' => 'Second'],
                ]
            )
        ]);


    $google = new Google($integration);
    $manager = new SpreadsheetManager($google, $integration);

    $manager->buildColumns();

    $form->update([
        'properties' => [
            ['id' => '000', 'name' => 'First name'],
            ['id' => '002', 'name' => 'Email'],
        ]
    ]);

    $integration->refresh();
    $columns = $manager->buildColumns();

    assertCount(3, $columns);
    assertEquals('First name', $columns[0]['name']);
    assertEquals('Second', $columns[1]['name']);
    assertEquals('Email', $columns[2]['name']);
});
