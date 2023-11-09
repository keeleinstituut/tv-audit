<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use App\Http\OpenApiHelpers\Schemas\EventRecords;
use AuditLogClient\Enums\AuditLogEventFailureType;
use AuditLogClient\Enums\AuditLogEventType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Event record representing a failed attempt at doing something (event_parameters structure unknown for the time being)',
    allOf: [
        new OA\Schema(ref: EventRecords\EventRecordCommonSchema::class),
        new OA\Schema(
            required: ['event_type', 'event_parameters', 'failure_type'],
            properties: [
                new OA\Property(property: 'failure_type', type: 'string', enum: AuditLogEventFailureType::class),
                new OA\Property(
                    property: 'event_type',
                    type: 'string',
                    enum: AuditLogEventType::class
                ),
                new OA\Property(property: 'event_parameters', type: 'object'),
            ],
        ),
    ]
)]
class FailedAttemptEventRecordSchema
{
}
