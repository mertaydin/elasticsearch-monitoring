<?php

return [
    'connection' => [
        'url' => env('ELASTICSEARCH_MONITORING_URL', 'http://127.0.0.1:9200'),
        'username' => env('ELASTICSEARCH_MONITORING_USERNAME'),
        'password' => env('ELASTICSEARCH_MONITORING_PASSWORD'),
        'api_key' => env('ELASTICSEARCH_MONITORING_API_KEY'),
        'timeout' => (int) env('ELASTICSEARCH_MONITORING_TIMEOUT', 10),
        'slow_response_ms' => (int) env('ELASTICSEARCH_MONITORING_SLOW_RESPONSE_MS', 1000),
        'verify' => (bool) env('ELASTICSEARCH_MONITORING_VERIFY', true),
    ],
    'collection' => [
        'retention_days' => 90,
    ],
    'dashboard' => [
        'path' => 'monitoring/elasticsearch',
        'locale' => 'en',
        'available_locales' => ['tr', 'en', 'zh_CN', 'hi', 'es', 'ar'],
    ],
    'alerts' => [
        'thresholds' => [
            'cpu_warning' => 80,
            'cpu_critical' => 90,
            'heap_warning' => 75,
            'heap_critical' => 85,
            'disk_warning' => 80,
            'disk_critical' => 90,
            'unassigned_shards_warning' => 1,
            'unassigned_shards_critical' => 5,
        ],
        'notifications' => [
            'cooldown_minutes' => 30,
            'channels' => [
                'mail' => [
                    'enabled' => false,
                    'recipients' => [],
                ],
                'slack' => [
                    'enabled' => false,
                    'webhook_url' => env('ELASTICSEARCH_MONITORING_SLACK_WEBHOOK'),
                ],
                'teams' => [
                    'enabled' => false,
                    'webhook_url' => env('ELASTICSEARCH_MONITORING_TEAMS_WEBHOOK'),
                ],
            ],
        ],
    ],
    'register_schedule' => false,
];
