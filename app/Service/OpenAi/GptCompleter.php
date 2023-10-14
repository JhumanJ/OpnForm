<?php

namespace App\Service\OpenAi;

use App\Service\OpenAi\Utils\JsonFixer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenAI\Client;
use OpenAI\Exceptions\ErrorException;

/**
 * Handles a GPT completion prompt with or without insert tag.
 * Also parses output.
 */
class GptCompleter
{
    const AI_MODEL = 'gpt-4';

    protected Client $openAi;
    protected mixed $result;
    protected array $completionInput;
    protected ?string $systemMessage;

    protected int $tokenUsed = 0;
    protected bool $useStreaming = false;

    public function __construct(string $apiKey, protected int $retries = 2, protected string $model = self::AI_MODEL)
    {
        $this->openAi = \OpenAI::client($apiKey);
    }

    public function setAiModel(string $model)
    {
        $this->model = $model;
        return $this;
    }

    public function setSystemMessage(string $systemMessage): self
    {
        $this->systemMessage = $systemMessage;
        return $this;
    }

    public function useStreaming(): self
    {
        $this->useStreaming = true;
        return $this;
    }

    public function completeChat(array $messages, int $maxTokens = 4096, float $temperature = 0.81): self
    {
        $this->computeChatCompletion($messages, $maxTokens, $temperature)
            ->queryCompletion();

        return $this;
    }

    public function getBool(): bool
    {
        switch (strtolower($this->result)) {
            case 'true':
                return true;
            case 'false':
                return false;
            default:
                throw new \InvalidArgumentException("Expected a boolean value, got {$this->result}");
        }
    }

    public function getArray(): array
    {
        for ($i = 0; $i < $this->retries; $i++) {
            $payload = Str::of($this->result)->trim();
            if ($payload->contains('```json')) {
                $payload = $payload->after('```json')->before('```');
            } else if ($payload->contains('```')) {
                $payload = $payload->after('```')->before('```');
            }
            $payload = $payload->toString();
            $exception = null;

            try {
                $newPayload = (new JsonFixer)->fix($payload);
                return json_decode($newPayload, true);
            } catch (\Aws\Exception\InvalidJsonException $e) {
                $exception = $e;
                Log::warning("Invalid JSON, retrying:");
                Log::warning($payload);
                $this->queryCompletion();
            }
        }
        throw $exception;
    }

    public function getHtml(): string
    {
        $payload = Str::of($this->result)->trim();
        if ($payload->contains('```html')) {
            $payload = $payload->after('```html')->before('```');
        } else if ($payload->contains('```')) {
            $payload = $payload->after('```')->before('```');
        }
        return $payload->toString();
    }

    public function getString(): string
    {
        return trim($this->result);
    }

    public function getTokenUsed(): int
    {
        return $this->tokenUsed;
    }

    protected function computeChatCompletion(array $messages, int $maxTokens = 4096, float $temperature = 0.81): self
    {
        if (isset($this->systemMessage) && $messages[0]['role'] !== 'system') {
            $messages = array_merge([[
                'role' => 'system',
                'content' => $this->systemMessage
            ]], $messages);
        }

        $completionInput = [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature
        ];

        $this->completionInput = $completionInput;
        return $this;
    }

    protected function queryCompletion(): self
    {
        if ($this->useStreaming) {
            return $this->queryStreamedCompletion();
        }

        try {
            Log::debug("Open AI query: " . json_encode($this->completionInput));
            $response = $this->openAi->chat()->create($this->completionInput);
        } catch (ErrorException $errorException) {
            // Retry once
            Log::warning("Open AI error, retrying: {$errorException->getMessage()}");
            $response = $this->openAi->chat()->create($this->completionInput);
        }
        $this->tokenUsed += $response->usage->totalTokens;
        $this->result = $response->choices[0]->message->content;
        return $this;
    }

    protected function queryStreamedCompletion(): self
    {
        Log::debug("Open AI query: " . json_encode($this->completionInput));
        $this->result = '';
        $response = $this->openAi->chat()->createStreamed($this->completionInput);
        foreach ($response as $chunk) {
            $choice = $chunk->choices[0];
            if (is_null($choice->delta->role)) {
                $this->result .= $choice->delta->content;
            }
        }

        return $this;
    }
}
