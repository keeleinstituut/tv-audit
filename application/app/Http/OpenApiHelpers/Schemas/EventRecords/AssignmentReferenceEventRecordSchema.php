<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use App\Http\OpenApiHelpers\Schemas\EventRecords;
use AuditLogClient\Enums\AuditLogEventType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Event record whose parameters hold a reference to an assignment',
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
                        AuditLogEventType::RejectAssignmentResult,
                        AuditLogEventType::ApproveAssignmentResult,
                        AuditLogEventType::CompleteAssignment,
                    ]
                ),
                new OA\Property(
                    property: 'event_parameters',
                    required: ['assignment_id', 'assignment_ext_id'],
                    properties: [
                        new OA\Property(property: 'assignment_id', type: 'string'),
                        new OA\Property(property: 'assignment_ext_id', type: 'string'),
                    ],
                    type: 'object',
                ),
            ],
        ),
    ]
)]
class AssignmentReferenceEventRecordSchema
{
}
