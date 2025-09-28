<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Sabberworm\CSS\Parser as CssParser;
use Sabberworm\CSS\Settings;
use Sabberworm\CSS\CSSList\AtRuleBlockList;
use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\RuleSet\AtRuleSet;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\Value\URL;
use Sabberworm\CSS\Value\CSSFunction;
use Throwable;

class CssOnlyRule implements ValidationRule
{
    /**
     * Validate the attribute is CSS-only (no HTML or scripts) with simple checks.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $css = (string) $value;

        // Parse CSS to ensure it is syntactically valid and free of HTML
        try {
            $settings = Settings::create()->beStrict();
            $document = (new CssParser($css, $settings))->parse();
        } catch (Throwable) {
            $fail('The :attribute must be valid CSS.');
            return;
        }

        // Validate allowed at-rules and URLs
        if (!$this->validateCssDocument($document)) {
            $fail('The :attribute contains disallowed CSS constructs.');
        }
    }

    private function validateCssDocument(Document $document): bool
    {
        // Allowed at-rules list (case-insensitive)
        $allowedAtRules = [
            '@media',
            '@supports',
            '@font-face',
            '@keyframes',
            '@-webkit-keyframes',
            '@import',
            '@page'
        ];

        // Check top-level contents
        foreach ($document->getContents() as $content) {
            if ($content instanceof AtRuleBlockList) {
                $atRule = '@' . strtolower($content->atRuleName());
                if (!in_array($atRule, array_map('strtolower', $allowedAtRules), true)) {
                    return false;
                }
            }

            // Validate @import at-rule args
            if ($content instanceof AtRuleSet) {
                $atRule = '@' . strtolower($content->atRuleName());
                if (!in_array($atRule, array_map('strtolower', $allowedAtRules), true)) {
                    return false;
                }
                if ($atRule === '@import') {
                    $params = strtolower(trim((string) $content->atRuleArgs()));
                    if (!$this->isAllowedUrlInParams($params)) {
                        return false;
                    }
                }
            }

            if ($content instanceof DeclarationBlock) {
                foreach ($content->getRules() as $rule) {
                    $value = $rule->getValue();
                    // Disallow legacy IE JS hooks
                    if (method_exists($rule, 'getRule')) {
                        $propName = strtolower((string) $rule->getRule());
                        if ($propName === 'behavior') {
                            return false;
                        }
                    }
                    // Traverse for URL values
                    foreach ($this->flattenValues($value) as $v) {
                        if ($v instanceof CSSFunction) {
                            if (strtolower($v->getName()) === 'expression') {
                                return false;
                            }
                        }
                        if ($v instanceof URL) {
                            $url = (string) $v->getURL();
                            if (!$this->isAllowedUrl($url)) {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    private function flattenValues($value): array
    {
        $values = [];
        if (is_object($value) && method_exists($value, 'getListComponents')) {
            foreach ($value->getListComponents() as $component) {
                $values = array_merge($values, $this->flattenValues($component));
            }
        } else {
            $values[] = $value;
        }
        return $values;
    }

    private function isAllowedUrlInParams(string $params): bool
    {
        // Extract url(...) or quoted URLs in @import parameters
        if (preg_match('/url\s*\(\s*([\"\"]?)(.*?)\1\s*\)/i', $params, $m)) {
            return $this->isAllowedUrl($m[2] ?? '');
        }
        if (preg_match('/^[\"\"](.*?)[\"\"]/', $params, $m)) {
            return $this->isAllowedUrl($m[1] ?? '');
        }
        // If no URL found, consider safe
        return true;
    }

    private function isAllowedUrl(string $url): bool
    {
        $url = trim($url);
        if ($url === '') {
            return true;
        }
        // Allow only http(s) and data:image/* URLs
        if (preg_match('/^https?:\/\//i', $url)) {
            return true;
        }
        // Allow only safe image data URLs (exclude SVG)
        if (preg_match('/^data:\s*image\/(png|gif|jpe?g|webp|avif|bmp)/i', $url)) {
            return true;
        }
        return false;
    }
}
