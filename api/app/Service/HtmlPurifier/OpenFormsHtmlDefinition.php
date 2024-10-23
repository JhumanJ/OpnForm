<?php

namespace App\Service\HtmlPurifier;

use HTMLPurifier_HTMLDefinition;
use Stevebauman\Purify\Definitions\Definition;
use Stevebauman\Purify\Definitions\Html5Definition;

class OpenFormsHtmlDefinition implements Definition
{
    public static function apply(HTMLPurifier_HTMLDefinition $definition)
    {
        Html5Definition::apply($definition);

        $definition->addAttribute('span', 'mention-field-id', 'Text');
        $definition->addAttribute('span', 'mention-field-name', 'Text');
        $definition->addAttribute('span', 'mention-fallback', 'Text');
        $definition->addAttribute('span', 'mention', 'Bool');
        $definition->addAttribute('span', 'contenteditable', 'Bool');
    }
}
