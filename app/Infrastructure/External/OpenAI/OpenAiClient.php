<?php

namespace App\Infrastructure\External\OpenAI;

use App\Domain\Ai\Contracts\AiClientInterface;
use App\Domain\Ai\Contracts\AiCompletionResult;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiClient implements AiClientInterface
{
    public function driverName(): string
    {
        return 'openai';
    }

    public function complete(string $systemPrompt, string $userPrompt, array $context = []): AiCompletionResult
    {
        $key = (string) config('ai.openai.key');
        if ($key === '') {
            throw new RuntimeException('OPENAI_API_KEY tanımlı değil.');
        }

        $model = (string) config('ai.openai.model', 'gpt-4o-mini');
        $baseUrl = rtrim((string) config('ai.openai.base_url', 'https://api.openai.com/v1'), '/');
        $timeout = (int) config('ai.timeout', 30);

        $response = Http::withToken($key)
            ->timeout($timeout)
            ->acceptJson()
            ->post($baseUrl.'/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.3,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('OpenAI isteği başarısız: HTTP '.$response->status());
        }

        /** @var array<string, mixed> $payload */
        $payload = $response->json() ?? [];
        $text = data_get($payload, 'choices.0.message.content');
        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('OpenAI boş yanıt döndü.');
        }

        $tokens = data_get($payload, 'usage.total_tokens');

        return new AiCompletionResult(
            text: trim($text),
            driver: $this->driverName(),
            model: is_string(data_get($payload, 'model')) ? (string) data_get($payload, 'model') : $model,
            tokensUsed: is_numeric($tokens) ? (int) $tokens : null,
            metadata: ['purpose' => $context['purpose'] ?? null],
        );
    }
}
