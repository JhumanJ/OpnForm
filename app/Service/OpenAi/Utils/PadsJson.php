<?php

namespace App\Service\OpenAi\Utils;

/**
 * Attempts to fix truncated JSON by padding contextual counterparts at the end.
 *
 * @author   Jitendra Adhikari <jiten.adhikary@gmail.com>
 * @license  MIT
 *
 * @internal
 *
 * @link     https://github.com/adhocore/php-json-fixer
 */
trait PadsJson
{
    public function pad($tmpJson)
    {
        if (! $this->inStr) {
            $tmpJson = \rtrim($tmpJson, ',');
            while ($this->lastToken() === ',') {
                $this->popToken();
            }
        }

        $tmpJson = $this->padLiteral($tmpJson);
        $tmpJson = $this->padObject($tmpJson);

        return $this->padStack($tmpJson);
    }

    protected function padLiteral($tmpJson)
    {
        if ($this->inStr) {
            return $tmpJson;
        }

        $match = \preg_match('/(tr?u?e?|fa?l?s?e?|nu?l?l?)$/', $tmpJson, $matches);

        if (! $match || null === $literal = $this->maybeLiteral($matches[1])) {
            return $tmpJson;
        }

        return \substr($tmpJson, 0, 0 - \strlen($matches[1])).$literal;
    }

    protected function padStack($tmpJson)
    {
        foreach (\array_reverse($this->stack, true) as $token) {
            if (isset($this->pairs[$token])) {
                $tmpJson .= $this->pairs[$token];
            }
        }

        return $tmpJson;
    }

    protected function padObject($tmpJson)
    {
        if (! $this->objectNeedsPadding($tmpJson)) {
            return $tmpJson;
        }

        $part = \substr($tmpJson, $this->objectPos + 1);
        if (\preg_match('/(\s*\"[^"]+\"\s*:\s*[^,]+,?)+$/', $part, $matches)) {
            return $tmpJson;
        }

        if ($this->inStr) {
            $tmpJson .= '"';
        }

        $tmpJson = $this->padIf($tmpJson, ':');
        $tmpJson = $tmpJson.$this->missingValue;

        if ($this->lastToken() === '"') {
            $this->popToken();
        }

        return $tmpJson;
    }

    protected function objectNeedsPadding($tmpJson)
    {
        $last = \substr($tmpJson, -1);
        $empty = $last === '{' && ! $this->inStr;

        return ! $empty && $this->arrayPos < $this->objectPos;
    }

    protected function padString($string)
    {
        $last = \substr($string, -1);
        $last2 = \substr($string, -2);

        if ($last2 === '\"' || $last !== '"') {
            return $string.'"';
        }

        // @codeCoverageIgnoreStart
        return null;
        // @codeCoverageIgnoreEnd
    }

    protected function padIf($string, $substr)
    {
        if (\substr($string, 0 - \strlen($substr)) !== $substr) {
            return $string.$substr;
        }

        return $string;
    }
}
