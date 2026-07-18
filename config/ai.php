<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "heuristic", "openai"
    | Tests and local default use heuristic (no external API key required).
    |
    */
    'driver' => env('AI_DRIVER', 'heuristic'),

    'timeout' => (int) env('AI_TIMEOUT', 30),

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    ],
];
