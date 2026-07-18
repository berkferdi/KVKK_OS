<?php

namespace App\Application\Services\Documents;

use App\Domain\Documents\ValueObjects\DocumentPlaceholderMap;

class DocumentRenderer
{
    public function render(string $body, DocumentPlaceholderMap $map): string
    {
        return (string) preg_replace_callback(
            '/\{\{\s*([a-z0-9_]+)\s*\}\}/i',
            function (array $matches) use ($map): string {
                $key = strtolower($matches[1]);

                return $map->get($key) ?? '';
            },
            $body,
        );
    }

    /**
     * @param  list<string>  $keys
     * @return list<string>
     */
    public function missingKeys(array $keys, DocumentPlaceholderMap $map): array
    {
        $missing = [];
        foreach ($keys as $key) {
            $normalized = strtolower($key);
            if (! $map->has($normalized)) {
                $missing[] = $normalized;
            }
        }

        return array_values(array_unique($missing));
    }
}
