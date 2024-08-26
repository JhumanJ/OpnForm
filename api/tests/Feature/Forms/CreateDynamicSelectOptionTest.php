<?php

use Tests\Helpers\FormSubmissionDataFactory;

it('can submit form with dyanamic select option', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $selectionsPreData = [];
    $form->properties = collect($form->properties)->map(function ($property) use (&$selectionsPreData) {
        if (in_array($property['type'], ['select', 'multi_select'])) {
            $property['allow_creation'] = true;
            $selectionsPreData[$property['id']] = ($property['type'] == 'select') ? 'New single select - '.time() : ['New multi select - '.time()];
        }

        return $property;
    })->toArray();
    $form->update();

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $selectionsPreData);
    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});
