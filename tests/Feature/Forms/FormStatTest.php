<?php

use Illuminate\Support\Facades\Artisan;

it('check formstat chart data', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, []);

    $views = [];
    $submissions = [];
    // Create 10 views & submissions for past days
    for ($i = 1; $i <= 10; $i++) {
        $date = now()->subDays($i);
        $dateString = $date->format('d-m-Y');

        $submission = $form->submissions()->create();
        $submission->created_at = $date;
        $submission->save();
        $view = $form->views()->create();
        $view->created_at = $date;
        $view->save();

        $views[$dateString] = isset($views[$dateString]) ? ($views[$dateString] + 1) : 1;
        $submissions[$dateString] = isset($submissions[$dateString]) ? ($submissions[$dateString] + 1) : 1;
    }

    // Run Command
    Artisan::call('forms:database-cleanup');

    // Create 5 views & submissions
    for ($i = 1; $i <= 5; $i++) {
        $form->views()->create();
        $form->submissions()->create();

        $dateString = now()->format('d-m-Y');
        $views[$dateString] = isset($views[$dateString]) ? ($views[$dateString] + 1) : 1;
        $submissions[$dateString] = isset($submissions[$dateString]) ? ($submissions[$dateString] + 1) : 1;
    }

    // Now check chart data
    $this->getJson(route('open.workspaces.form.stats', [$workspace->id, $form->id]))
        ->assertSuccessful()
        ->assertJson(function (\Illuminate\Testing\Fluent\AssertableJson $json) use ($views, $submissions) {
            return $json->whereType('views', 'array')
                ->whereType('submissions', 'array')
                ->where('views', function ($values) use ($views) {
                    foreach ($values as $date => $count) {
                        if ((isset($views[$date]) && $views[$date] != $count) || (!isset($views[$date]) && $count != 0)) {
                            return false;
                        }
                    }

                    return true;
                })
                ->where('submissions', function ($values) use ($submissions) {
                    ray($values, $submissions);
                    foreach ($values as $date => $count) {
                        if ((isset($submissions[$date]) && $submissions[$date] != $count) || (!isset($submissions[$date]) && $count != 0)) {
                            return false;
                        }
                    }

                    return true;
                })
                ->etc();
        });
});
