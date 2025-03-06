<?php

namespace App\Console\Commands;

use App\Models\Forms\Form;
use App\Models\Template;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FormTitleMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:form-title-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One Time Only -- Migrate Form Title to new Form Title';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->formMigration();

        $this->formTemplateMigration();

        $this->line('Migration Done');
    }

    public function formMigration()
    {
        $this->info('Starting forms migration...');

        Form::chunk(100, function ($forms) {
            foreach ($forms as $form) {
                if ($form?->hide_title ?? false) {
                    continue;
                }

                $properties = $form->properties ?? [];

                array_unshift($properties, [
                    'type' => 'nf-text',
                    'content' => '<h1>' . $form->title . '</h1>',
                    'name' => 'Title',
                    'align' => $form->layout_rtl ? 'right' : 'left',
                    'id' => Str::uuid()
                ]);

                $form->properties = $properties;
                $form->timestamps = false;
                $form->save();
            }
        });
    }

    public function formTemplateMigration()
    {
        $this->info('Starting forms template migration...');

        Template::chunk(100, function ($templates) {
            foreach ($templates as $template) {
                $structure = $template->structure ?? [];
                if (!$structure) {
                    continue;
                }

                $properties = $structure['properties'] ?? [];

                array_unshift($properties, [
                    'type' => 'nf-text',
                    'content' => '<h1>' . $structure['title'] . '</h1>',
                    'name' => 'Title',
                    'align' => isset($structure['layout_rtl']) && $structure['layout_rtl'] ? 'right' : 'left',
                    'id' => Str::uuid()
                ]);

                $structure['properties'] = $properties;
                $template->structure = $structure;
                $template->timestamps = false;
                $template->save();
            }
        });
    }
}
