<?php

use App\Events\IncomingAuditLogMessageEvent;
use App\Events\TestAuditLogEvent;
use SyncTools\Events\MessageEventFactory;

return [
    /*
    |--------------------------------------------------------------------------
    | AMQP connection properties
    |--------------------------------------------------------------------------
    */
    'connection' => [
        'host' => env('AMQP_HOST', 'localhost'),
        'port' => env('AMQP_PORT', 5672),
        'username' => env('AMQP_USER', 'guest'),
        'password' => env('AMQP_PASSWORD', 'guest'),
        'vhost' => env('AMQP_VHOST', '/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AMQP consumer properties (remove if not needed)
    |--------------------------------------------------------------------------
    */
    'consumer' => [
        'queues' => [
            [
                'queue' => env('AUDIT_LOG_EVENTS_QUEUE', 'audit-log-events'),
                'bindings' => [
                    ['exchange' => env('AUDIT_LOG_EVENTS_EXCHANGE', 'audit-log-events')],
                ],
            ],
            [
                'queue' => 'audit-log-events2',
                'bindings' => [],
            ],
        ],
        'events' => [
            'mode' => MessageEventFactory::MODE_QUEUE,
            'map' => [
                env('AUDIT_LOG_EVENTS_QUEUE', 'audit-log-events') => IncomingAuditLogMessageEvent::class,
                'audit-log-events2' => TestAuditLogEvent::class,
            ],
        ],
        'enable_manual_acknowledgement' => true,
    ],
    'publisher' => [
        'exchanges' => [
            [
                'exchange' => env('AUDIT_LOG_EVENTS_EXCHANGE', 'audit-log-events'),
                'type' => 'topic'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Log AMQP properties (remove if not needed)
    |--------------------------------------------------------------------------
    */
    'audit_logs' => [
        'exchange' => env('AUDIT_LOG_EVENTS_EXCHANGE'),
        'trace_id_http_header' => env('AUDIT_LOG_TRACE_ID_HTTP_HEADER'),
        'required_jwt_realm_role' => env('AUDIT_LOG_REQUIRED_JWT_REALM_ROLE', 'publish-audit-logs'),
    ],
];
