<?php

namespace App\Open;

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
        return $this->replaceMentions();
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
        $value = collect($this->data)->filter(function ($item) use ($fieldId) {
            return $item['id'] === $fieldId;
        })->first()['value'] ?? null;

        if (is_object($value)) {
            return (array) $value;
        }

        return $value;
    }
}
