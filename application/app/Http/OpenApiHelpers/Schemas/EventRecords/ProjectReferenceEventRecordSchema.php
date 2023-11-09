<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use App\Http\OpenApiHelpers\Schemas\EventRecords;
use AuditLogClient\Enums\AuditLogEventType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Event record whose parameters hold a reference to a project',
    allOf: [
        new OA\Schema(ref: EventRecords\EventRecordCommonSchema::class),
        new OA\Schema(
            required: ['event_type', 'event_parameters', 'failure_type'],
            properties: [
                new OA\Property(property: 'failure_type', type: 'null'),
                new OA\Property(
                    property: 'event_type',
                    type: 'string',
                    enum: [AuditLogEventType::FinishProject]
                ),
                new OA\Property(
                    property: 'event_parameters',
                    required: [
                        'project_id',
                        'project_ext_id',
                    ],
                    properties: [
                        new OA\Property(property: 'project_id', type: 'string'),
                        new OA\Property(property: 'project_ext_id', type: 'string'),
                    ],
                    type: 'object',
                ),
            ],
        ),
    ]
)]
class ProjectReferenceEventRecordSchema
{
}
