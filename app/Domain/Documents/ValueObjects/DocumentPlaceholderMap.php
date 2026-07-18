<?php

namespace App\Domain\Documents\ValueObjects;

final class DocumentPlaceholderMap
{
    /**
     * @param  array<string, string>  $values
     */
    public function __construct(
        private readonly array $values,
    ) {}

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->values;
    }

    public function get(string $key): ?string
    {
        if (! array_key_exists($key, $this->values)) {
            return null;
        }

        $value = $this->values[$key];

        return $value === '' ? null : $value;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }
}
