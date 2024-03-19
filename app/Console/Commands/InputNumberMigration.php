<?php

namespace App\Console\Commands;

use App\Models\Forms\Form;
use Illuminate\Console\Command;

class InputNumberMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:input-number-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One Time Only -- Separate input type for number (rating, scale, slider)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Form::chunk(
            100,
            function ($forms) {
                foreach ($forms as $form) {
                    $this->line('Process For Form: ' . $form->id . ' - ' . $form->slug);

                    $form->properties = collect($form->properties)->map(function ($property) {
                        if ($property['type'] === 'number') {
                            // Rating
                            if (isset($property['is_rating']) && $property['is_rating']) {
                                $this->line('Rating Field');
                                $property['type'] = 'rating';
                                unset($property['is_rating']);
                            }

                            // Scale
                            if (isset($property['is_scale']) && $property['is_scale']) {
                                $this->line('Scale Field');
                                $property['type'] = 'scale';
                                unset($property['is_scale']);
                            }

                            // Slider
                            if (isset($property['is_slider']) && $property['is_slider']) {
                                $this->line('Slider Field');
                                $property['type'] = 'slider';
                                unset($property['is_slider']);
                            }
                        }
                        return $property;
                    })->toArray();
                    $form->update();
                }
            }
        );

        $this->line('Migration Done');
    }
}
