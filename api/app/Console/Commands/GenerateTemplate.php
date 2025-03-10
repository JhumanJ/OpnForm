<?php

namespace App\Console\Commands;

use App\Models\Template;
use App\Service\AI\Prompts\Form\GenerateFormPrompt;
use App\Service\AI\Prompts\Template\GenerateTemplateMetadataPrompt;
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
    protected $signature = 'form:generate {prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new form template from a prompt';

    public const MAX_RELATED_TEMPLATES = 8;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get form structure using the form prompt class
        $formData = GenerateFormPrompt::run($this->argument('prompt'));

        // Generate all template metadata using the consolidated prompt
        $metadata = GenerateTemplateMetadataPrompt::run($this->argument('prompt'));

        // Extract metadata components
        $formShortDescription = $metadata['short_description'];
        $formDescription = $metadata['detailed_description'];
        $formTitle = $metadata['title'];
        $industry = $metadata['industries'];
        $types = $metadata['types'];
        $formQAs = $metadata['qa_content'];

        // Get Related Templates
        $relatedTemplates = $this->getRelatedTemplates($industry, $types);

        // Get image cover URL
        $imageUrl = $this->getImageCoverUrl($metadata['image_search_query']);

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

        // Set reverse related Templates
        $this->setReverseRelatedTemplates($template);

        $this->info(front_url('/form-templates/' . $template->slug));

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
}
