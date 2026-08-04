<?php

namespace App\Domain\Ai\Contracts;

interface AiClientInterface
{
    /**
     * @param  array<string, mixed>  $context  Redacted, PII-safe context
     */
    public function complete(string $systemPrompt, string $userPrompt, array $context = []): AiCompletionResult;

    public function driverName(): string;
}
