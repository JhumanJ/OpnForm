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
    const AI_MODEL = 'gpt-3.5-turbo';

    protected Client $openAi;
    protected mixed $result;
    protected array $completionInput;
    protected ?string $systemMessage;

    protected int $tokenUsed = 0;

    public function __construct(string $apiKey, protected int $retries = 2)
    {
        $this->openAi = \OpenAI::client($apiKey);
    }

    public function setSystemMessage(string $systemMessage): self
    {
        $this->systemMessage = $systemMessage;
        return $this;
    }

    public function completeChat(array $messages, int $maxTokens = 512, float $temperature = 0.81): self
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
        $payload = Str::of($this->result)->trim();
        if ($payload->contains('```json')) {
            $payload = $payload->after('```json')->before('```');
        } else if ($payload->contains('```')) {
            $payload = $payload->after('```')->before('```');
        }
        $payload = $payload->toString();
        $exception = null;

        for ($i = 0; $i < $this->retries; $i++) {
            try {
                $payload = (new JsonFixer)->fix($payload);
                return json_decode($payload, true);
            } catch (\Aws\Exception\InvalidJsonException $e) {
                $exception = $e;
                Log::warning("Invalid JSON, retrying:");
                Log::warning($payload);
                Log::warning(json_encode($this->completionInput));
                $this->queryCompletion();
            }
        }
        throw $exception;
    }

    public function getString(): string
    {
        return trim($this->result);
    }

    public function getTokenUsed(): int
    {
        return $this->tokenUsed;
    }

    protected function computeChatCompletion(array $messages, int $maxTokens = 512, float $temperature = 0.81): self
    {
        if (isset($this->systemMessage)) {
            $messages = array_merge([
                'role' => 'system',
                'content' => $this->systemMessage
            ], $messages);
        }

        $completionInput = [
            'model' => self::AI_MODEL,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature
        ];

        $this->completionInput = $completionInput;
        return $this;
    }

    protected function queryCompletion(): self {
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
}
