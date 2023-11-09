<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use App\Http\OpenApiHelpers\Schemas\EventRecords;
use AuditLogClient\Enums\AuditLogEventType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Event record whose parameters describe a projects export query',
    allOf: [
        new OA\Schema(ref: EventRecords\EventRecordCommonSchema::class),
        new OA\Schema(
            required: ['event_type', 'event_parameters', 'failure_type'],
            properties: [
                new OA\Property(property: 'failure_type', type: 'null'),
                new OA\Property(
                    property: 'event_type',
                    type: 'string',
                    enum: [AuditLogEventType::ExportProjectsReport]
                ),
                new OA\Property(
                    property: 'event_parameters',
                    required: [
                        'query_start_date',
                        'query_end_date',
                        'query_status',
                    ],
                    properties: [
                        new OA\Property(property: 'query_start_date', type: 'string', nullable: true),
                        new OA\Property(property: 'query_end_date', type: 'string', nullable: true),
                        new OA\Property(property: 'query_status', type: 'string', nullable: true),
                    ],
                    type: 'object',
                ),
            ],
        ),
    ]
)]
class ProjectExportQueryEventRecordSchema
{
}
