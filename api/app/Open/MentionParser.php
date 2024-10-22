<?php

namespace App\Open;

use DOMDocument;
use DOMXPath;

class MentionParser
{
    private $content;
    private $data;

    public function __construct($content, $data)
    {
        $this->content = $content;
        $this->data = $data;
    }

    public function parse()
    {
        $doc = new DOMDocument();
        // Disable libxml errors and use internal errors
        $internalErrors = libxml_use_internal_errors(true);

        // Wrap the content in a root element to ensure it's valid XML
        $wrappedContent = '<root>' . $this->content . '</root>';

        // Load HTML, using UTF-8 encoding
        $doc->loadHTML(mb_convert_encoding($wrappedContent, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Restore libxml error handling
        libxml_use_internal_errors($internalErrors);

        $xpath = new DOMXPath($doc);
        $mentionElements = $xpath->query("//span[@mention]");

        foreach ($mentionElements as $element) {
            $fieldId = $element->getAttribute('mention-field-id');
            $fallback = $element->getAttribute('mention-fallback');
            $value = $this->getData($fieldId);

            if ($value !== null) {
                $textNode = $doc->createTextNode(is_array($value) ? implode(', ', $value) : $value);
                $element->parentNode->replaceChild($textNode, $element);
            } elseif ($fallback) {
                $textNode = $doc->createTextNode($fallback);
                $element->parentNode->replaceChild($textNode, $element);
            } else {
                $element->parentNode->removeChild($element);
            }
        }

        // Extract and return the processed HTML content
        $result = $doc->saveHTML($doc->getElementsByTagName('root')->item(0));

        // Remove the root tags we added
        $result = preg_replace('/<\/?root>/', '', $result);

        // Trim whitespace and convert HTML entities back to UTF-8 characters
        $result = trim(html_entity_decode($result, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return $result;
    }

    private function replaceMentions()
    {
        $pattern = '/<span[^>]*mention-field-id="([^"]*)"[^>]*mention-fallback="([^"]*)"[^>]*>.*?<\/span>/';
        return preg_replace_callback($pattern, function ($matches) {
            $fieldId = $matches[1];
            $fallback = $matches[2];
            $value = $this->getData($fieldId);

            if ($value !== null) {
                if (is_array($value)) {
                    return implode(' ', array_map(function ($v) {
                        return $v;
                    }, $value));
                }
                return $value;
            } elseif ($fallback) {
                return $fallback;
            }
            return '';
        }, $this->content);
    }

    private function getData($fieldId)
    {
        $value = collect($this->data)->firstWhere('id', $fieldId)['value'] ?? null;

        if (is_object($value)) {
            return (array) $value;
        }

        return $value;
    }
}
