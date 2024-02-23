<?php

namespace App\Service;

use App\Models\Forms\Form;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Generates meta per-route matching. This is useful because Google, Twitter and Facebook struggle to load meta tags
 * injected dynamically by JavaScript. This class allows us to inject meta tags in the HTML head tag.
 *
 * Here's how to use this class
 * - Add a pattern to URL_PATTERNS
 * - Then choose between a static meta or a dynamic meta:
 *      - If the content is dynamic (ex: needs to retrieve data from the database), then add a method to this class for
 *        the corresponding pattern. The method should be named "getMyPatternName" (where pattern name is
 *        my_pattern_name) and it should return an array of meta tags.
 *      - If the content is static, then add meta tags to the PATTERN_STATIC_META array.
 */
class SeoMetaResolver
{
    private array $patternData = [];

    public const URL_PATTERNS = [
        'welcome' => '/',
        'form_show' => '/forms/{slug}',
        'login' => '/login',
        'register' => '/register',
        'reset_password' => '/password/reset',
        'privacy_policy' => '/privacy-policy',
        'terms_conditions' => '/terms-conditions',
        'integrations' => '/integrations',
        'templates' => '/form-templates',
        'templates_show' => '/form-templates/{slug}',
        'templates_types_show' => '/form-templates/types/{slug}',
        'templates_industries_show' => '/form-templates/industries/{slug}',
    ];

    /**
     * Metas for simple route (without needing to access DB)
     */
    public const PATTERN_STATIC_META = [
        'login' => [
            'title' => 'Login',
        ],
        'register' => [
            'title' => 'Create your account',
        ],
        'reset_password' => [
            'title' => 'Reset your password',
        ],
        'privacy_policy' => [
            'title' => 'Our Privacy Policy',
        ],
        'terms_conditions' => [
            'title' => 'Our Terms & Conditions',
        ],
        'integrations' => [
            'title' => 'Our Integrations',
        ],
        'templates' => [
            'title' => 'Templates',
            'description' => 'Our collection of beautiful templates to create your own forms!',
        ],
    ];

    public const META_CACHE_DURATION = 60 * 60 * 12; // 12 hours

    public const META_CACHE_KEY_PREFIX = 'seo_meta_';

    public function __construct(private Request $request)
    {
    }

    /**
     * Returns the right metas for a given route, caches meta for 1 hour.
     */
    public function getMetas(): array
    {
        $cacheKey = self::META_CACHE_KEY_PREFIX.urlencode($this->request->path());

        return Cache::remember($cacheKey, now()->addSeconds(self::META_CACHE_DURATION), function () {
            $pattern = $this->resolvePattern();

            if ($this->hasPatternMetaGetter($pattern)) {
                // Custom function for pattern
                try {
                    return array_merge($this->getDefaultMeta(), $this->{'get'.Str::studly($pattern).'Meta'}());
                } catch (\Exception $e) {
                    return $this->getDefaultMeta();
                }
            } elseif (in_array($pattern, array_keys(self::PATTERN_STATIC_META))) {
                // Simple meta for pattern
                $meta = self::PATTERN_STATIC_META[$pattern];
                if (isset($meta['title'])) {
                    $meta['title'] .= $this->titleSuffix();
                }
                if (isset($meta['image'])) {
                    $meta['image'] = asset($meta['image']);
                }

                return array_merge($this->getDefaultMeta(), $meta);
            }

            return $this->getDefaultMeta();
        });
    }

    /**
     * Simulates the Laravel router to match route with Metas
     *
     * @return string
     */
    private function resolvePattern()
    {
        foreach (self::URL_PATTERNS as $patternName => $patternData) {
            $path = rtrim($this->request->getPathInfo(), '/') ?: '/';

            $route = (new Route('GET', $patternData, fn () => ''))->bind($this->request);
            if (preg_match($route->getCompiled()->getRegex(), rawurldecode($path))) {
                $this->patternData = $route->parameters();

                return $patternName;
            }
        }

        return 'default';
    }

    /**
     * Determine if a get mutator exists for a pattern.
     *
     * @param  string  $key
     * @return bool
     */
    private function hasPatternMetaGetter($key)
    {
        return method_exists($this, 'get'.Str::studly($key).'Meta');
    }

    private function titleSuffix()
    {
        return ' · '.config('app.name');
    }

    private function getDefaultMeta(): array
    {
        return [
            'title' => 'Create beautiful forms for free'.$this->titleSuffix(),
            'description' => "Create beautiful forms for free. Unlimited fields, unlimited submissions. It's free and it takes less than 1 minute to create your first form.",
            'image' => asset('/img/social-preview.jpg'),
        ];
    }

    private function getFormShowMeta(): array
    {
        $form = Form::whereSlug($this->patternData['slug'])->firstOrFail();

        $meta = [];
        if ($form->is_pro && $form->seo_meta->page_title) {
            $meta['title'] = $form->seo_meta->page_title;
        } else {
            $meta['title'] = $form->title.$this->titleSuffix();
        }

        if ($form->is_pro && $form->seo_meta->page_description) {
            $meta['description'] = $form->seo_meta->page_description;
        } elseif ($form->description) {
            $meta['description'] = Str::of($form->description)->limit(160);
        }

        if ($form->is_pro && $form->seo_meta->page_thumbnail) {
            $meta['image'] = $form->seo_meta->page_thumbnail;
        } elseif ($form->cover_picture) {
            $meta['image'] = $form->cover_picture;
        }

        return $meta;
    }

    private function getTemplatesShowMeta(): array
    {
        $template = Template::whereSlug($this->patternData['slug'])->firstOrFail();

        return [
            'title' => $template->name.$this->titleSuffix(),
            'description' => Str::of($template->short_description)->limit(140).' | Customize any template and create your own form in minutes.',
            'image' => $template->image_url,
        ];
    }

    private function getTemplatesTypesShowMeta(): array
    {
        $types = json_decode(file_get_contents(resource_path('data/forms/templates/types.json')), true);
        $type = $types[array_search($this->patternData['slug'], array_column($types, 'slug'))];

        return [
            'title' => $type['meta_title'],
            'description' => Str::of($type['meta_description'])->limit(140),
        ];
    }

    private function getTemplatesIndustriesShowMeta(): array
    {
        $industries = json_decode(file_get_contents(resource_path('data/forms/templates/industries.json')), true);
        $industry = $industries[array_search($this->patternData['slug'], array_column($industries, 'slug'))];

        return [
            'title' => $industry['meta_title'],
            'description' => Str::of($industry['meta_description'])->limit(140),
        ];
    }
}
