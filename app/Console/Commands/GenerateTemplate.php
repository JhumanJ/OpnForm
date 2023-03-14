<?php

namespace App\Console\Commands;

use App\Models\Template;
use App\Service\OpenAi\GptCompleter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GenerateTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:make-form-template {prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new form template from a prompt';

    const FORM_STRUCTURE_PROMPT = <<<EOD
        I created a form builder. Forms are represented as Json objects. Here's an example form:
        ```json
         {
                "title": "Contact Form",
                "properties": [
                    {
                        "help": null,
                        "name": "What's your name?",
                        "type": "text",
                        "hidden": false,
                        "prefill": null,
                        "required": true,
                        "placeholder": null
                    },
                    {
                        "help": null,
                        "name": "Email",
                        "type": "email",
                        "hidden": false,
                        "prefill": null,
                        "required": true,
                        "placeholder": null
                    },
                    {
          "help": null,
          "name": "How would you rate your overall experience?",
          "type": "select",
          "hidden": false,
          "select": {
            "options": [
              {
                "id": "Below Average",
                "name": "Below Average"
              },
              {
                "id": "Average",
                "name": "Average"
              },
              {
                "id": "Above Average",
                "name": "Above Average"
              }
            ]
          },
          "prefill": null,
          "required": true,
          "placeholder": null,
        },
                    {
                        "help": null,
                        "name": "Subject",
                        "type": "text",
                        "hidden": false,
                        "prefill": null,
                        "required": true,
                        "placeholder": null
                    },
                    {
                        "help": null,
                        "name": "How can we help?",
                        "type": "text",
                        "hidden": false,
                        "prefill": null,
                        "required": true,
                        "multi_lines": true,
                        "placeholder": null,
                        "generates_uuid": false,
                        "max_char_limit": "2000",
                        "hide_field_name": false,
                        "show_char_limit": false,
                        "generates_auto_increment_id": false
                    },
                    {
                        "help": null,
                        "name": "Have any attachments?",
                        "type": "files",
                        "hidden": false,
                        "prefill": null,
                        "placeholder": null
                    }
                ],
                "description": "<p>Looking for a real person to speak to?</p><p>We're here for you!  Just drop in your queries below and we'll connect with you as soon as we can.</p>",
                "re_fillable": false,
                "use_captcha": false,
                "redirect_url": null,
                "submitted_text": "<p>Great, we've received your message. We'll get back to you as soon as we can :)</p>",
                "uppercase_labels": false,
                "submit_button_text": "Submit",
                "re_fill_button_text": "Fill Again",
                "color": "#3B82F6"
            }
        ```
        The form properties can have one of the following types: 'text', 'number', 'select', 'multi_select', 'date', 'files', 'checkbox', 'url', 'email', 'phone_number'.
        All form properties objects need to have the keys 'help', 'name', 'type', 'hidden', 'placeholder', 'prefill'.

        For the type "select" and "multi_select", the input object must have a key "select" (or "multi_select") that's mapped to an object like this one:
        ```json
        {
           "options": [
                 {"id":"Option 1","name":"Option 1"},
                 {"id":"Pption 2","name":"Option 2"}
           ]
        }
        ```

        Give me the JSON code only, for the following form: "[REPLACE]"
        Do not ask me for more information about required properties or types, suggest me a form structure instead.
    EOD;

    const FORM_DESCRIPTION_PROMPT = <<<EOD
        I own a form builder online named OpnForm. It's free to use. Give me a description for a template page for the following form: [REPLACE]. Explain what the form is about, and that it takes seconds to duplicate the template to create your own version it and to start getting some submissions.
    EOD;

    const FORM_QAS_PROMPT = <<<EOD
        Now give me 3 to 5 question and answers to put on the form template page. The questions should be about the reasons for this template (when to use, why, target audience, goal etc.) and OpnForm's usage. Reply only with a valid JSON, being an array of object containing the keys "question" and "answer".
    EOD;

    const FORM_TITLE_PROMPT = <<<EOD
        Finally give me a title for the template. It should be short and to the point, without any quotes.
    EOD;

    const FORM_IMG_KEYWORDS_PROMPT = <<<EOD
        I want to add an image to illustrate this form template page. Give me a releveant search query for unsplash. Reply only with a valid JSON like this:
        ```json
        {
           "search_query": ""
        }
        ```
    EOD;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get form structture
        $completer = new GptCompleter(config('services.openai.api_key'));
        $completer->completeChat([
            ["role" => "system", "content" => "You are a robot helping to generate forms."],
            ["role" => "user", "content" => Str::of(self::FORM_STRUCTURE_PROMPT)->replace('[REPLACE]', $this->argument('prompt'))->toString()]
        ],3000);
        $formData = $completer->getArray();

        // Now get description and QAs
        $formDescriptionPrompt = Str::of(self::FORM_DESCRIPTION_PROMPT)->replace('[REPLACE]', $this->argument('prompt'))->toString();
        $formDescription = $completer->completeChat([
            ["role" => "system", "content" => "You are a robot helping to generate forms."],
            ["role" => "user", "content" => $formDescriptionPrompt]
        ])->getString();
        $formQAs = $completer->completeChat([
            ["role" => "system", "content" => "You are a robot helping to generate forms."],
            ["role" => "user", "content" => $formDescriptionPrompt],
            ["role" => "assistant", "content" => $formDescription],
            ["role" => "user", "content" => self::FORM_QAS_PROMPT]
        ])->getArray();
        $formTitle = $completer->completeChat([
            ["role" => "system", "content" => "You are a robot helping to generate forms."],
            ["role" => "user", "content" => $formDescriptionPrompt],
            ["role" => "assistant", "content" => $formDescription],
            ["role" => "user", "content" => self::FORM_TITLE_PROMPT]
        ])->getString();

        // Finally get keyworks for image cover
        $formCoverKeyworks = $completer->completeChat([
            ["role" => "system", "content" => "You are a robot helping to generate forms."],
            ["role" => "user", "content" => $formDescriptionPrompt],
            ["role" => "assistant", "content" => $formDescription],
            ["role" => "user", "content" => self::FORM_IMG_KEYWORDS_PROMPT]
        ])->getArray();
        $imageUrl = $this->getImageCoverUrl($formCoverKeyworks['search_query']);

        $template = $this->createFormTemplate($formData, $formTitle, $formDescription, $formQAs, $imageUrl);
        $this->info('/templates/' . $template->slug);

        return Command::SUCCESS;
    }

    /**
     * Get an image cover URL for the template using unsplash API
     */
    private function getImageCoverUrl($searchQuery): ?string
    {
        $url = 'https://api.unsplash.com/search/photos?query=' . urlencode($searchQuery) . '&client_id=' . config('services.unslash.access_key');
        $response = Http::get($url)->json();
        ray($response, $url);
        if (isset($response['results'][0]['urls']['regular'])) {
            return $response['results'][0]['urls']['regular'];
        }
        return null;
    }

    private function createFormTemplate(array $formData, string $formTitle, string $formDescription, array $formQAs, ?string $imageUrl)
    {
        // Add property uuids
        foreach ($formData['properties'] as &$property) {
            $property['id'] = Str::uuid()->toString();
        }

        return Template::create([
            'name' => $formTitle,
            'description' => $formDescription,
            'questions' => $formQAs,
            'structure' => $formData,
            'image_url' => $imageUrl,
        ]);
    }
}
