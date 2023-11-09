<?php

use App\Events\IncomingAuditLogMessageEvent;
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
                'queue' => env('AUDIT_LOG_EVENTS_QUEUE'),
                'bindings' => [
                    ['exchange' => env('AUDIT_LOG_EVENTS_EXCHANGE')],
                ],
            ],
        ],
        'events' => [
            'mode' => MessageEventFactory::MODE_QUEUE,
            'map' => [
                env('AUDIT_LOG_EVENTS_QUEUE') => IncomingAuditLogMessageEvent::class,
            ],
        ],
        'enable_manual_acknowledgment' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Log AMQP properties (remove if not needed)
    |--------------------------------------------------------------------------
    */
    'audit_logs' => [
        'exchange' => env('AUDIT_LOG_EVENTS_EXCHANGE'),
        'trace_id_http_header' => env('AUDIT_LOG_TRACE_ID_HTTP_HEADER'),
    ],
];
