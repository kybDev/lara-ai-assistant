<?php

return [
    'route' => [
        'prefix' => 'ai-assistant',
        'middleware' => ['web', 'auth'],
    ],

    'ollama' => [
        'base_url' => env('OLLAMA_BASE_URL', 'http://127.0.0.1:11434'),
        'model' => env('OLLAMA_MODEL', 'qwen3:4b'),
        'timeout' => (int) env('OLLAMA_TIMEOUT', 180),
        'think' => false,
        'system_prompt' => 'You are a helpful assistant. Answer clearly and accurately. You do not have access to application databases or private records.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Model discovery
    |--------------------------------------------------------------------------
    |
    | Discovery describes model metadata only. Models must be explicitly
    | allowlisted here before a future retrieval feature may use them.
    |
    */
    'model_directories' => [
        [
            'path' => 'app/Models',
            'namespace' => 'App\\Models',
        ],
    ],

    'enabled_models' => [],

    'excluded_models' => [],
];
