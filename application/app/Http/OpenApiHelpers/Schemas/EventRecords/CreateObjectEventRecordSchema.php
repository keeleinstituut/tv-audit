<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use App\Http\OpenApiHelpers\Schemas\EventRecords;
use AuditLogClient\Enums\AuditLogEventObjectType;
use AuditLogClient\Enums\AuditLogEventType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Event record whose parameters describe an attempt to create an object',
    allOf: [
        new OA\Schema(ref: EventRecords\EventRecordCommonSchema::class),
        new OA\Schema(
            required: ['event_type', 'event_parameters', 'failure_type'],
            properties: [
                new OA\Property(property: 'failure_type', type: 'null'),
                new OA\Property(
                    property: 'event_type',
                    type: 'string',
                    enum: [AuditLogEventType::CreateObject]
                ),
                new OA\Property(
                    property: 'event_parameters',
                    required: [
                        'object_type',
                        'object_data',
                    ],
                    properties: [
                        new OA\Property(property: 'object_type', type: 'string', enum: AuditLogEventObjectType::class),
                        new OA\Property(property: 'object_data', type: 'object'),
                    ],
                    type: 'object',
                ),
            ],
        ),
    ]
)]
class CreateObjectEventRecordSchema
{
}
