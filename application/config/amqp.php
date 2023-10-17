<?php

use App\Events\AuditLogEvent;
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
                'queue' => 'audit-log-events',
                'bindings' => [
                    ['exchange' => 'audit-log-events'],
                ],
            ],
        ],
        'events' => [
            'mode' => MessageEventFactory::MODE_QUEUE,
            'map' => [
                'audit-log-events' => AuditLogEvent::class,
            ],
        ],
        'enable_manual_acknowledgment' => true,
    ],
];
