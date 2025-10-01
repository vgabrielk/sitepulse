<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SitePulse Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the SitePulse Analytics
    | platform. You can modify these values to customize the behavior of
    | the application.
    |
    */

    'widget' => [
        'url' => env('SITEPULSE_WIDGET_URL', 'http://localhost:8000/widget'),
        'cache_ttl' => 3600, // 1 hour
        'max_events_per_batch' => 50,
        'batch_timeout' => 5, // seconds
    ],

    'rate_limiting' => [
        'api_per_minute' => env('SITEPULSE_RATE_LIMIT_PER_MINUTE', 60),
        'widget_per_minute' => 100,
        'login_per_minute' => 5,
        'registration_per_hour' => 3,
        'webhook_per_minute' => 100,
        'export_per_hour' => 10,
    ],

    'privacy' => [
        'anonymize_ips' => env('SITEPULSE_ANONYMIZE_IPS', true),
        'respect_do_not_track' => true,
        'cookie_consent_required' => true,
        'data_retention_days' => [
            'free' => 30,
            'basic' => 90,
            'premium' => 365,
            'enterprise' => -1, // unlimited
        ],
        'gdpr_compliance' => true,
        'ccpa_compliance' => true,
    ],

    'security' => [
        'api_key_length' => 32,
        'webhook_secret_length' => 32,
        'session_timeout' => 30, // minutes
        'max_login_attempts' => 5,
        'lockout_duration' => 15, // minutes
        'require_https' => env('SITEPULSE_REQUIRE_HTTPS', false),
        'allowed_origins' => [
            'https://sitepulse.com',
            'https://www.sitepulse.com',
        ],
    ],

    'plans' => [
        'free' => [
            'name' => 'Free',
            'price' => 0,
            'limits' => [
                'monthly_visits' => 1000,
                'monthly_events' => 5000,
                'sites' => 1,
                'reviews' => 50,
                'exports' => 0,
                'api_calls' => 1000,
                'retention_days' => 30,
            ],
        ],
        'basic' => [
            'name' => 'Basic',
            'price' => 9.99,
            'limits' => [
                'monthly_visits' => 10000,
                'monthly_events' => 50000,
                'sites' => 3,
                'reviews' => 500,
                'exports' => 10,
                'api_calls' => 10000,
                'retention_days' => 90,
            ],
        ],
        'premium' => [
            'name' => 'Premium',
            'price' => 29.99,
            'limits' => [
                'monthly_visits' => 100000,
                'monthly_events' => 500000,
                'sites' => 10,
                'reviews' => 5000,
                'exports' => 100,
                'api_calls' => 100000,
                'retention_days' => 365,
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price' => 99.99,
            'limits' => [
                'monthly_visits' => -1, // unlimited
                'monthly_events' => -1,
                'sites' => -1,
                'reviews' => -1,
                'exports' => -1,
                'api_calls' => -1,
                'retention_days' => -1,
            ],
        ],
    ],

    'webhooks' => [
        'enabled' => true,
        'timeout' => 10, // seconds
        'retry_attempts' => 3,
        'retry_delay' => 60, // seconds
        'events' => [
            'event.created',
            'session.created',
            'review.created',
            'site.created',
            'limit.exceeded',
            'plan.upgraded',
            'plan.downgraded',
            'subscription.cancelled',
            'subscription.reactivated',
            'report.daily',
            'report.weekly',
            'report.monthly',
        ],
    ],

    'analytics' => [
        'aggregation_interval' => 60, // seconds
        'real_time_enabled' => true,
        'heatmap_enabled' => true,
        'session_timeout' => 30, // minutes
        'max_events_per_session' => 1000,
        'max_sessions_per_site' => 10000,
    ],

    'exports' => [
        'formats' => ['csv', 'excel', 'json'],
        'max_records' => 100000,
        'chunk_size' => 1000,
        'timeout' => 300, // seconds
    ],

    'notifications' => [
        'email' => [
            'enabled' => true,
            'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@sitepulse.com'),
            'from_name' => env('MAIL_FROM_NAME', 'SitePulse Analytics'),
        ],
        'webhook' => [
            'enabled' => true,
            'timeout' => 10,
        ],
    ],

    'admin' => [
        'email' => env('SITEPULSE_ADMIN_EMAIL', 'admin@sitepulse.com'),
        'dashboard_refresh' => 30, // seconds
        'system_checks' => [
            'database' => true,
            'redis' => true,
            'queue' => true,
            'storage' => true,
            'mail' => true,
        ],
    ],

    'maintenance' => [
        'enabled' => env('SITEPULSE_MAINTENANCE_MODE', false),
        'message' => 'SitePulse is currently under maintenance. Please check back later.',
        'allowed_ips' => [
            '127.0.0.1',
            '::1',
        ],
    ],
];
