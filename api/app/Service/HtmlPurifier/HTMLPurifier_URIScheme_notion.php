<?php

namespace App\Service\HtmlPurifier;

class HTMLPurifier_URIScheme_notion extends \HTMLPurifier_URIScheme
{
    public $browsable = true;

    public $may_omit_host = true;

    public function doValidate(&$uri, $config, $context)
    {
        if ($uri->host == 'www.notion.so' || $uri->host == 'notion.so') {
            return true;
        } else {
            return false;
        }
    }
}
