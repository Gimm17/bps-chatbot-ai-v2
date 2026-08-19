<?php

return [
    'default' => env('AI_DEFAULT_PROVIDER', 'limitrouter'),

    'demo_mode' => env('AI_DEMO_MODE', true),

    'timeout' => (int) env('AI_TIMEOUT', 30),

    'providers' => [
        'limitrouter' => [
            'base_url' => env('LIMITROUTER_BASE_URL', 'https://limitrouter.com/v1'),
            'api_key' => env('LIMITROUTER_API_KEY', ''),
            'default_model' => env('LIMITROUTER_DEFAULT_MODEL', 'gemini-3.7-flash'),
        ],
    ],
];
