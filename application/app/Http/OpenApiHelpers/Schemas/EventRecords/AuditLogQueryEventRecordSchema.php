<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use App\Http\OpenApiHelpers\Schemas\EventRecords;
use AuditLogClient\Enums\AuditLogEventType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Event record whose parameters describe an audit log query',
    allOf: [
        new OA\Schema(ref: EventRecords\EventRecordCommonSchema::class),
        new OA\Schema(
            required: ['event_type', 'event_parameters', 'failure_type'],
            properties: [
                new OA\Property(property: 'failure_type', type: 'null'),
                new OA\Property(
                    property: 'event_type',
                    type: 'string',
                    enum: [
                        AuditLogEventType::SearchLogs,
                        AuditLogEventType::ExportLogs,
                    ]
                ),
                new OA\Property(
                    property: 'event_parameters',
                    required: [
                        'query_start_datetime',
                        'query_end_datetime',
                        'query_event_type',
                        'query_text',
                        'query_department_id',
                    ],
                    properties: [
                        new OA\Property(property: 'query_start_datetime', type: 'string', nullable: true),
                        new OA\Property(property: 'query_end_datetime', type: 'string', nullable: true),
                        new OA\Property(property: 'query_event_type', type: 'string', nullable: true),
                        new OA\Property(property: 'query_text', type: 'string', nullable: true),
                        new OA\Property(property: 'query_department_id', type: 'string', nullable: true),
                    ],
                    type: 'object',
                ),
            ],
        ),
    ]
)]
class AuditLogQueryEventRecordSchema
{
}
