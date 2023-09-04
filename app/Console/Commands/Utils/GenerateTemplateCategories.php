<?php

namespace App\Console\Commands\Utils;

use App\Service\OpenAi\GptCompleter;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateTemplateCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'template:generate-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Load industries from json file
        $industries = json_decode(file_get_contents(resource_path('data/forms/templates/industries.json')), true);
        // Load types from json file
        $types = json_decode(file_get_contents(resource_path('data/forms/templates/types.json')), true);

        $completer = (new GptCompleter(config('services.openai.api_key')))
            ->setAiModel('gpt-3.5-turbo-16k')
            ->useStreaming()
            ->setSystemMessage('You are an assistant helping to generate forms.');

        // Now foreach industry, transform the meta_title, the description and meta_description
        foreach ($types as &$type) {
            $completer->completeChat([
                ["role" => "user", "content" => Str::of(
                    <<<EOT
                        I am building a set of form templates for an open-source form builder named OpnForm. Each template
                        is assigned to form types (between 1 and 3). I need you to help generate information for the form type "[INDUSTRY]".
                        The information I need is the following:
                        - description: a short description explaining who this is targeted at, what kind of form type it is, how can they use OpnForm and our templates (2 to 3 relevant features max).
                        - meta_title: a short but click-bait title that will be used in the meta title of the page.
                        - meta_description: a short description about how people needing that type of form can use OpnForm and our templates.

                        Example description: "Running a business and managing orders? OpnForm offers Order Form Templates that can simplify your work! By using these form templates, you can collect order details easily, streamline your process and benefit from OpnForm's integrations. Use an order form template below, customize it to fit your needs, and see how OpnForm can upgrade your ordering process."

                        Things you can mention about OpnForm (do not mention or invent anything else):
                        - OpnForm is an open-source form builder
                        - It has email notifications for both the form owner and the form submitter
                        - Forms can be fully customized and branded
                        - Custom domain can be uses to host forms
                        - Forms can be embedded on any website
                        - Forms can be integrated with Slack, Discord, and many other tools via Zapier or webhooks.
                        - Forms can be protected via a password
                        - You can add a captcha to your form
                        - You can add custom code to your form
                        - You can pre-fill a form via url parameters
                        - You can close form at a given date, or after a certain number of submissions
                        - forms can be uses to edit existing form submissions (respondent can edit their own submissions)
                        - forms can have multiple pages

                        Answer only with a valid json object for the "[INDUSTRY]" form type, containing the following keys: description, meta_title, meta_description.
                    EOT
                )->replace('[INDUSTRY]', $type['name'])->toString()]
            ], 6000);
            $type = array_merge($type, $completer->getArray());
        }

        echo json_encode($types, JSON_PRETTY_PRINT);

        return Command::SUCCESS;
    }
}
