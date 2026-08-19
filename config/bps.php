<?php

return [
    'enabled' => filter_var(env('BPS_ENABLED', true), FILTER_VALIDATE_BOOLEAN),

    'key' => env('BPS_WEBAPI_KEY', ''),

    'base_url' => env('BPS_WEBAPI_BASE_URL', 'https://webapi.bps.go.id'),

    'http' => [
        'timeout_sec' => (int) env('BPS_HTTP_TIMEOUT_SEC', 15),
    ],

    'agent' => [
        'max_tool_calls' => (int) env('BPS_AGENT_MAX_TOOL_CALLS', 4),
        'timeout_sec' => (int) env('BPS_AGENT_TIMEOUT_SEC', 60),
    ],

    'cache' => [
        'enabled' => filter_var(env('BPS_CACHE_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        'ttl_hours' => (int) env('BPS_CACHE_TTL_HOURS', 24),
        'prefix' => 'bps:v2:',
    ],

    'live_tests' => filter_var(env('BPS_LIVE_TESTS', false), FILTER_VALIDATE_BOOLEAN),
];
