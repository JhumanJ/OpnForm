<?php

it('can create and delete Workspace', function () {
    $user = $this->actingAsUser();

    for ($i = 1; $i <= 3; $i++) {
        $this->postJson(route('open.workspaces.create'), [
            'name' => 'Workspace Test - '.$i,
            'icon' => '🧪',
        ])
            ->assertSuccessful()
            ->assertJson([
                'type' => 'success',
                'message' => 'Workspace created.',
            ]);
    }

    expect($user->workspaces()->count())->toBe(3);

    $i = 0;
    foreach ($user->workspaces as $workspace) {
        $i++;
        if ($i !== 3) {
            $this->deleteJson(route('open.workspaces.delete', $workspace->id))
                ->assertSuccessful()
                ->assertJson([
                    'type' => 'success',
                    'message' => 'Workspace deleted.',
                ]);
        } else {
            // Last workspace can not delete
            $this->deleteJson(route('open.workspaces.delete', $workspace->id))
                ->assertStatus(403);
        }
    }
});
