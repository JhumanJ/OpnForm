<?php

namespace App\Http\Controllers\Content;

use App\Models\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SitemapController extends Controller
{
    public function index(Request $request)
    {
        return [
            ...$this->getTemplatesUrls(),
        ];
    }

    private function getTemplatesUrls()
    {
        $urls = [];
        Template::where('publicly_listed', true)->chunk(100, function ($templates) use (&$urls) {
            foreach ($templates as $template) {
                $urls[] = [
                    'loc' => '/templates/' . $template->slug,
                ];
            }
        });

        return $urls;
    }
}
