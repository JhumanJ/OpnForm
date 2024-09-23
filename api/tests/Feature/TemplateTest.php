<?php

it('can create template', function () {
    $user = $this->createUser([
        'email' => 'admin@opnform.com',
    ]);
    $this->actingAsUser($user);

    // Create Form
    $workspace = $this->createUserWorkspace($user);
    $form = $this->makeForm($user, $workspace);

    // Create Template
    $templateData = [
        'name' => 'Demo Template',
        'slug' => 'demo_template',
        'short_description' => 'Short description here...',
        'description' => 'Some long description here...',
        'image_url' => 'https://d3ietpyl4f2d18.cloudfront.net/6c35a864-ee3a-4039-80a4-040b6c20ac60/img/pages/welcome/product_cover.jpg',
        'publicly_listed' => true,
        'form' => $form->getAttributes(),
        'questions' => [['question' => 'Question 1', 'answer' => 'Answer 1 will be here...']],
    ];
    $this->postJson(route('templates.create', $templateData))
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Template was created.',
        ]);
});
