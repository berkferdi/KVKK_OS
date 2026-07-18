<?php

namespace App\Domain\Ai\Contracts;

final class AiCompletionResult
{
    public function __construct(
        public readonly string $text,
        public readonly string $driver,
        public readonly ?string $model = null,
        public readonly ?int $tokensUsed = null,
        public readonly array $metadata = [],
    ) {}
}
