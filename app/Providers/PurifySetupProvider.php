<?php

namespace App\Providers;

use App\Service\HtmlPurifier\HTMLPurifier_URIScheme_notion;
use Illuminate\Support\ServiceProvider;

class PurifySetupProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \HTMLPurifier_URISchemeRegistry::instance()->register('notion', new HTMLPurifier_URIScheme_notion());
    }
}
