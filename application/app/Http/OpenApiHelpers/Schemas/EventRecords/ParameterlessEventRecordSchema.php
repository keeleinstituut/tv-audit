<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use App\Http\OpenApiHelpers\Schemas\EventRecords;
use AuditLogClient\Enums\AuditLogEventType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Event record which requires no parameters',
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
                        AuditLogEventType::LogIn,
                        AuditLogEventType::LogOut,
                        AuditLogEventType::ExportInstitutionUsers,
                        AuditLogEventType::SelectInstitution,
                    ]
                ),
                new OA\Property(property: 'event_parameters', type: 'string', enum: [null], nullable: true),
            ],
        ),
    ]
)]
class ParameterlessEventRecordSchema
{
}
