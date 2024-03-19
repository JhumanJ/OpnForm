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

    public const MAX_RELATED_TEMPLATES = 8;

    public const FORM_STRUCTURE_PROMPT = <<<'EOD'
        You are an AI assistant for OpnForm, a form builder and your job is to build a form for our user.

        Forms are represented as Json objects. Here's an example form:
        ```json
         {
                "title": "Contact Us",
                "properties": [
                    {
                        "help": null,
                        "name": "What's your name?",
                        "type": "text",
                        "hidden": false,
                        "required": true,
                        "placeholder": "Steve Jobs"
                    },
                    {
                        "help": "We will never share your email with anyone else.",
                        "name": "Email",
                        "type": "email",
                        "hidden": false,
                        "required": true,
                        "placeholder": "steve@apple.com"
                    },
                    {
                      "help": null,
                      "name": "How would you rate your overall experience?",
                      "type": "select",
                      "hidden": false,
                      "select": {
                        "options": [
                            {"name": 1, "value": 1},
                            {"name": 2, "value": 2},
                            {"name": 3, "value": 3},
                            {"name": 4, "value": 4},
                            {"name": 5, "value": 5}
                        ]
                      },
                      "prefill": 5,
                      "required": true,
                      "placeholder": null
                    },
                    {
                        "help": null,
                        "name": "Subject",
                        "type": "text",
                        "hidden": false,
                        "required": true,
                        "placeholder": null
                    },
                    {
                        "help": null,
                        "name": "How can we help?",
                        "type": "text",
                        "hidden": false,
                        "required": true,
                        "multi_lines": true,
                        "placeholder": null,
                        "generates_uuid": false,
                        "max_char_limit": "2000",
                        "hide_field_name": false,
                        "show_char_limit": false
                    },
                    {
                        "help": "Upload any relevant files here.",
                        "name": "Have any attachments?",
                        "type": "files",
                        "hidden": false,
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
                "color": "#64748b"
            }
        ```
        The form properties can only have one of the following types: 'text', 'number', 'rating', 'scale','slider', 'select', 'multi_select', 'date', 'files', 'checkbox', 'url', 'email', 'phone_number', 'signature'.
        All form properties objects need to have the keys 'help', 'name', 'type', 'hidden', 'placeholder', 'prefill'.
        The placeholder property is optional (can be "null") and is used to display a placeholder text in the input field.
        The help property is optional (can be "null") and is used to display extra information about the field.

        For the type "select" and "multi_select", the input object must have a key "select" (or "multi_select") that's mapped to an object like this one:
        ```json
        {
           "options": [
              {"name": 1, "value": 1},
              {"name": 2, "value": 2},
              {"name": 3, "value": 3},
              {"name": 4, "value": 4}
           ]
        }
        ```

        For "rating" you can set the field property "rating_max_value" to set the maximum value of the rating.
        For "scale" you can set the field property "scale_min_value", "scale_max_value" and "scale_step_value" to set the minimum, maximum and step value of the scale.
        For "slider" you can set the field property "slider_min_value", "slider_max_value" and "slider_step_value" to set the minimum, maximum and step value of the slider.

        If the form is too long, you can paginate it by adding a page break block in the list of properties:
        ```json
        {
            "name":"Page Break",
            "next_btn_text":"Next",
            "previous_btn_text":"Previous",
            "type":"nf-page-break",
        }
        ```

        If you need to add more context to the form, you can add text blocks:
        ```json
        {
            "name":"My Text",
            "type":"nf-text",
            "content": "<p>This is a text block.</p>"
        }
        ```

        Give me the valid JSON object only, representing the following form: "[REPLACE]"
        Do not ask me for more information about required properties or types, only suggest me a form structure.
    EOD;

    public const FORM_DESCRIPTION_PROMPT = <<<'EOD'
        You are an AI assistant for OpnForm, a form builder and your job is to help us build form templates for our users.
        Give me some valid html code (using only h2, p, ul, li html tags) for the following form template page: "[REPLACE]".

        The html code should have the following structure:
        - A paragraph explaining what the template is about
        - A paragraph explaining why and when to use such a form
        - A paragraph explaining who is the target audience and why it's a great idea to build this form
        - A paragraph explaining that OpnForm is the best tool to build this form. They can duplicate this template in a few seconds, and integrate with many other tools through our webhook or zapier integration.
        Each paragraph (except for the first one) MUST start with with a h2 tag containing a title for this paragraph.
    EOD;

    public const FORM_SHORT_DESCRIPTION_PROMPT = <<<'EOD'
        I own a form builder online named OpnForm. It's free to use.
        Give me a 1 sentence description for the following form template page: "[REPLACE]". It should be short and concise, but still explain what the form is about.
    EOD;

    public const FORM_INDUSTRY_PROMPT = <<<'EOD'
        You are an AI assistant for OpnForm, a form builder and your job is to help us build form templates for our users.
        I am creating a form template: "[REPLACE]". You must assign the template to industries. Return a list of industries (minimum 1, maximum 3 but only if very relevant) and order them by relevance (most relevant first).

        Here are the only industries you can choose from: [INDUSTRIES]
        Do no make up any new type, only use the ones listed above.

        Reply only with a valid JSON array, being an array of string. Order assigned industries from the most relevant to the less relevant.
        Ex: { "industries": ["banking_forms","customer_service_forms"]}
    EOD;

    public const FORM_TYPES_PROMPT = <<<'EOD'
        You are an AI assistant for OpnForm, a form builder and your job is to help us build form templates for our users.
        I am creating a form template: "[REPLACE]". You must assign the template to one or more types. Return a list of types (minimum 1, maximum 3 but only if very accurate) and order them by relevance (most relevant first).

        Here are the only types you can choose from: [TYPES]
        Do no make up any new type, only use the ones listed above.

        Reply only with a valid JSON array, being an array of string. Order assigned types from the most relevant to the less relevant.
        Ex: { "types": ["consent_forms","award_forms"]}
    EOD;

    public const FORM_QAS_PROMPT = <<<'EOD'
        Now give me 4 to 6 question and answers to put on the form template page. The questions should be about the reasons for this template (when to use, why, target audience, goal etc.).
        The questions should also explain why OpnForm is the best option to create this form (open-source, free to use, integrations etc).
        Reply only with a valid JSON, being an array of object containing the keys "question" and "answer".
    EOD;

    public const FORM_TITLE_PROMPT = <<<'EOD'
        Finally give me a title for the template. It must contain or end with "template". It should be short and to the point, without any quotes.
    EOD;

    public const FORM_IMG_KEYWORDS_PROMPT = <<<'EOD'
        I want to add an image to illustrate this form template page. Give me a relevant search query for unsplash. Reply only with a valid JSON like this:
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
        // Get form structure
        $completer = (new GptCompleter(config('services.openai.api_key')))
            ->useStreaming()
            ->setSystemMessage('You are an assistant helping to generate forms.');
        $completer->expectsJson()->completeChat([
            ['role' => 'user', 'content' => Str::of(self::FORM_STRUCTURE_PROMPT)->replace('[REPLACE]', $this->argument('prompt'))->toString()],
        ]);
        $formData = $completer->getArray();
        $formData = self::cleanAiOutput($formData);

        $completer->doesNotExpectJson();
        $formDescriptionPrompt = Str::of(self::FORM_DESCRIPTION_PROMPT)->replace('[REPLACE]', $this->argument('prompt'))->toString();
        $formShortDescription = $completer->completeChat([
            ['role' => 'user', 'content' => Str::of(self::FORM_SHORT_DESCRIPTION_PROMPT)->replace('[REPLACE]', $this->argument('prompt'))->toString()],
        ])->getString();
        // If description is between quotes, remove quotes
        $formShortDescription = Str::of($formShortDescription)->replaceMatches('/^"(.*)"$/', '$1')->toString();

        // Get industry & types
        $completer->expectsJson();
        $industry = $this->getIndustries($completer, $this->argument('prompt'));
        $types = $this->getTypes($completer, $this->argument('prompt'));

        // Get Related Templates
        $relatedTemplates = $this->getRelatedTemplates($industry, $types);

        // Now get description and QAs
        $completer->doesNotExpectJson();
        $formDescription = $completer->completeChat([
            ['role' => 'user', 'content' => $formDescriptionPrompt],
        ])->getHtml();

        $completer->expectsJson();
        $formCoverKeywords = $completer->completeChat([
            ['role' => 'user', 'content' => $formDescriptionPrompt],
            ['role' => 'assistant', 'content' => $formDescription],
            ['role' => 'user', 'content' => self::FORM_IMG_KEYWORDS_PROMPT],
        ])->getArray();
        $imageUrl = $this->getImageCoverUrl($formCoverKeywords['search_query']);

        $formQAs = $completer->completeChat([
            ['role' => 'user', 'content' => $formDescriptionPrompt],
            ['role' => 'assistant', 'content' => $formDescription],
            ['role' => 'user', 'content' => self::FORM_QAS_PROMPT],
        ])->getArray();
        $completer->doesNotExpectJson();
        $formTitle = $completer->completeChat([
            ['role' => 'user', 'content' => $formDescriptionPrompt],
            ['role' => 'assistant', 'content' => $formDescription],
            ['role' => 'user', 'content' => self::FORM_TITLE_PROMPT],
        ])->getString();

        $template = $this->createFormTemplate(
            $formData,
            $formTitle,
            $formDescription,
            $formShortDescription,
            $formQAs,
            $imageUrl,
            $industry,
            $types,
            $relatedTemplates
        );
        $this->info('/form-templates/' . $template->slug);

        // Set reverse related Templates
        $this->setReverseRelatedTemplates($template);

        return Command::SUCCESS;
    }

    /**
     * Get an image cover URL for the template using unsplash API
     */
    private function getImageCoverUrl($searchQuery): ?string
    {
        $url = 'https://api.unsplash.com/search/photos?query=' . urlencode($searchQuery) . '&client_id=' . config('services.unsplash.access_key');
        $response = Http::get($url)->json();
        $photoIndex = rand(0, max(count($response['results']) - 1, 10));
        if (isset($response['results'][$photoIndex]['urls']['regular'])) {
            return Str::of($response['results'][$photoIndex]['urls']['regular'])->replace('w=1080', 'w=600')->toString();
        }

        return null;
    }

    private function getIndustries(GptCompleter $completer, string $formPrompt): array
    {
        $industriesString = Template::getAllIndustries()->pluck('slug')->join(', ');

        return $completer->completeChat([
            ['role' => 'user', 'content' => Str::of(self::FORM_INDUSTRY_PROMPT)
                ->replace('[REPLACE]', $formPrompt)
                ->replace('[INDUSTRIES]', $industriesString)
                ->toString()],
        ])->getArray()['industries'];
    }

    private function getTypes(GptCompleter $completer, string $formPrompt): array
    {
        $typesString = Template::getAllTypes()->pluck('slug')->join(', ');

        return $completer->completeChat([
            ['role' => 'user', 'content' => Str::of(self::FORM_TYPES_PROMPT)
                ->replace('[REPLACE]', $formPrompt)
                ->replace('[TYPES]', $typesString)
                ->toString()],
        ])->getArray()['types'];
    }

    private function getRelatedTemplates(array $industries, array $types): array
    {
        $templateScore = [];
        Template::chunk(100, function ($otherTemplates) use ($industries, $types, &$templateScore) {
            foreach ($otherTemplates as $otherTemplate) {
                $industryOverlap = count(array_intersect($industries ?? [], $otherTemplate->industry ?? []));
                $typeOverlap = count(array_intersect($types ?? [], $otherTemplate->types ?? []));
                $score = $industryOverlap + $typeOverlap;
                if ($score > 1) {
                    $templateScore[$otherTemplate->slug] = $score;
                }
            }
        });
        arsort($templateScore); // Sort by Score

        return array_slice(array_keys($templateScore), 0, self::MAX_RELATED_TEMPLATES);
    }

    private function createFormTemplate(
        array $formData,
        string $formTitle,
        string $formDescription,
        string $formShortDescription,
        array $formQAs,
        ?string $imageUrl,
        array $industry,
        array $types,
        array $relatedTemplates
    ) {
        return Template::create([
            'name' => $formTitle,
            'description' => $formDescription,
            'short_description' => $formShortDescription,
            'questions' => $formQAs,
            'structure' => $formData,
            'image_url' => $imageUrl,
            'publicly_listed' => true,
            'industries' => $industry,
            'types' => $types,
            'related_templates' => $relatedTemplates,
        ]);
    }

    private function setReverseRelatedTemplates(Template $newTemplate)
    {
        if (!$newTemplate || count($newTemplate->related_templates) === 0) {
            return;
        }

        $templates = Template::whereIn('slug', $newTemplate->related_templates)->get();
        foreach ($templates as $template) {
            if (count($template->related_templates) < self::MAX_RELATED_TEMPLATES) {
                $template->update(['related_templates' => array_merge($template->related_templates, [$newTemplate->slug])]);
            }
        }
    }

    public static function cleanAiOutput(array $formData): array
    {
        // Add property uuids, improve form with options
        foreach ($formData['properties'] as &$property) {
            $property['id'] = Str::uuid()->toString(); // Column ID

            // Fix types
            if ($property['type'] == 'rating') {
                $property['rating_max_value'] = $property['rating_max_value'] ?? 5;
            } elseif ($property['type'] == 'scale') {
                $property['scale_min_value'] = $property['scale_min_value'] ?? 1;
                $property['scale_max_value'] = $property['scale_max_value'] ?? 5;
                $property['scale_step_value'] = $property['scale_step_value'] ?? 1;
            } elseif ($property['type'] == 'slider') {
                $property['slider_min_value'] = $property['slider_min_value'] ?? 0;
                $property['slider_max_value'] = $property['slider_max_value'] ?? 100;
                $property['slider_step_value'] = $property['slider_step_value'] ?? 1;
            }

            if (($property['type'] == 'select' && count($property['select']['options']) <= 4)
                || ($property['type'] == 'multi_select' && count($property['multi_select']['options']) <= 4)
            ) {
                $property['without_dropdown'] = true;
            }
        }

        // Clean data
        $formData['title'] = Str::of($formData['title'])->replace('"', '')->toString();

        return $formData;
    }
}
