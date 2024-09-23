<?php

use App\Models\Forms\Form;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Form::chunk(100, function ($forms) {
            foreach ($forms as $form) {
                $properties = $form->properties;
                if (!empty($form->description)) {
                    array_unshift($properties, [
                        'type' => 'nf-text',
                        'content' => $form->description,
                        'name' => 'Description',
                        'id' => Str::uuid()
                    ]);
                    $form->properties = $properties;
                    $form->timestamps = false;
                    $form->save();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Form::chunk(100, function ($forms) {
            foreach ($forms as $form) {
                $properties = $form->properties;
                if (!empty($properties) && $properties[0]['type'] === 'nf-text' && $properties[0]['name'] === 'Description') {
                    array_shift($properties);
                    $form->properties = $properties;
                    $form->save();
                }
            }
        });
    }
};
